<?php

namespace app\controllers;

use app\models\Product;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\ProductImage;
use yii\web\Response;
use Yii;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends AdminController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'delete-image' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
   public function actionView($id)
{
    $model = $this->findModel($id);
    $session = Yii::$app->session;

    // Otvaramo sesiju ako već nije otvorena
    if (!$session->isActive) {
        $session->open();
    }

    // Dohvaćamo niz pregledanih proizvoda iz sesije (ako ne postoji, kreiramo prazan niz)
    $viewedProducts = $session->get('viewed_products', []);

    // Ako ID ovog proizvoda NIJE u nizu, znači da ga korisnik vidi prvi put u ovoj sesiji
    if (!in_array($id, $viewedProducts)) {
        
        // Povećavamo brojač u bazi
        Product::updateAllCounters(['views_count' => 1], ['id' => $id]);
        
        // Dodajemo ID proizvoda u niz pregledanih artikala
        $viewedProducts[] = $id;
        
        // Spremamo ažurirani niz natrag u sesiju
        $session->set('viewed_products', $viewedProducts);
        
        // Opcionalno: ažuriramo i trenutni model koji šaljemo u view kako bi se odmah vidio novi broj
        $model->views_count += 1;
    }

    return $this->render('view', [
        'model' => $model,
    ]);
}


    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Product();
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                
                $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
                
                if ($model->imageFiles) {
                    $uploadDir = Yii::getAlias('@webroot/uploads/products/');
                    FileHelper::createDirectory($uploadDir);

                    foreach ($model->imageFiles as $index => $file) {
                        $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
                        $filePath = $uploadDir . $fileName;

                        if ($file->saveAs($filePath)) {
                            $imgModel = new ProductImage();
                            $imgModel->product_id = $model->id;
                            $imgModel->path = 'uploads/products/' . $fileName;
                            $imgModel->sort_order = $index;
                            // ISPRAVLJENO: Koristi se 1 i 0 umjesto true/false
                            $imgModel->is_main = ($index === 0) ? 1 : 0; 
                            $imgModel->created_at = date('Y-m-d H:i:s');
                            
                            if (!$imgModel->save()) {
                                Yii::error($imgModel->getErrors(), 'image_upload');
                                throw new \yii\web\ServerErrorHttpException('Greška pri spremanju slike u bazu: ' . json_encode($imgModel->getErrors()));
                            }
                        }
                    }
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->updated_at = date('Y-m-d H:i:s');

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            
            if ($model->imageFiles) {
                $uploadDir = Yii::getAlias('@webroot/uploads/products/');
                FileHelper::createDirectory($uploadDir);

                $maxSortOrder = (int) ProductImage::find()
                    ->where(['product_id' => $model->id])
                    ->max('sort_order');

                $hasExistingImages = ProductImage::find()
                    ->where(['product_id' => $model->id])
                    ->exists();

                foreach ($model->imageFiles as $index => $file) {
                    $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
                    $filePath = $uploadDir . $fileName;

                    if ($file->saveAs($filePath)) {
                        $imgModel = new ProductImage();
                        $imgModel->product_id = $model->id;
                        $imgModel->path = 'uploads/products/' . $fileName;
                        $imgModel->sort_order = $maxSortOrder + $index + 1;
                        
                        // ISPRAVLJENO: Koristi se 1 i 0 umjesto true/false
                        $imgModel->is_main = (!$hasExistingImages && $index === 0) ? 1 : 0;
                        $imgModel->created_at = date('Y-m-d H:i:s');
                        
                        if (!$imgModel->save()) {
                            Yii::error($imgModel->getErrors(), 'image_upload');
                            throw new \yii\web\ServerErrorHttpException('Greška pri spremanju slike u bazu: ' . json_encode($imgModel->getErrors()));
                        }
                    }
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Briše pojedinačnu sliku putem AJAX-a i po potrebi postavlja novu glavnu sliku.
     */
    public function actionDeleteImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $image = ProductImage::findOne($id);
            
            if ($image !== null) {
                $productId = $image->product_id;
                $wasMain = $image->is_main;
                
                $fullPath = Yii::getAlias('@webroot/') . $image->path;
                if (file_exists($fullPath) && is_file($fullPath)) {
                    unlink($fullPath);
                }
                
                if ($image->delete()) {
                    $newMainId = null;

                    // Ako je obrisana slika bila glavna (kod nas je to 1), postavi prvu sljedeću slobodnu
                    if ($wasMain == 1) {
                        $nextImage = ProductImage::find()
                            ->where(['product_id' => $productId])
                            ->orderBy(['sort_order' => SORT_ASC])
                            ->one();
                            
                        if ($nextImage) {
                            $nextImage->is_main = 1; // ISPRAVLJENO: Postavlja se 1 umjesto true
                            $nextImage->save(false);
                            $newMainId = $nextImage->id;
                        }
                    }
                    
                    return [
                        'success' => true, 
                        'wasMain' => ($wasMain == 1),
                        'newMainId' => $newMainId
                    ];
                }
                
                return ['success' => false, 'message' => 'Pogreška pri brisanju zapisa iz baze podataka.'];
            }
            
            return ['success' => false, 'message' => 'Slika nije pronađena.'];
        }
        
        return ['success' => false, 'message' => 'Neispravan zahtjev.'];
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
