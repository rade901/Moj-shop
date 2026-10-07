<?php

namespace app\controllers;

use app\models\Post;
use app\models\PostSearch;
use app\controllers\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\models\PostImage; // Model za tablicu s višestrukim slikama posta
use yii\web\Response;
use Yii;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends AdminController
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
     * Lists all Post models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * DODANO: Displays a single Post model.
     * Bez ove metode ruta post/view javlja 404 grešku.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Post();
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                // Dohvaćanje više ugrađenih datoteka iz polja 'imageFiles'
                $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');

                if ($model->imageFiles) {
                    $uploadDir = Yii::getAlias('@webroot/uploads/posts/');
                    FileHelper::createDirectory($uploadDir);

                    foreach ($model->imageFiles as $index => $file) {
                        $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
                        $filePath = $uploadDir . $fileName;

                        if ($file->saveAs($filePath)) {
                            $imgModel = new PostImage();
                            $imgModel->post_id = $model->id;
                            $imgModel->path = 'uploads/posts/' . $fileName;
                            $imgModel->sort_order = $index;
                            $imgModel->is_main = ($index === 0) ? 1 : 0;
                            $imgModel->created_at = date('Y-m-d H:i:s');
                            $imgModel->updated_at = date('Y-m-d H:i:s');

                            if (!$imgModel->save()) {
                                Yii::error($imgModel->getErrors(), 'image_upload');
                                throw new \yii\web\ServerErrorHttpException('Greška pri spremanju slike posta u bazu: ' . json_encode($imgModel->getErrors()));
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
     * Updates an existing Post model.
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
                $uploadDir = Yii::getAlias('@webroot/uploads/posts/');
                FileHelper::createDirectory($uploadDir);

                $maxSortOrder = (int) PostImage::find()
                    ->where(['post_id' => $model->id])
                    ->max('sort_order');

                $hasExistingImages = PostImage::find()
                    ->where(['post_id' => $model->id])
                    ->exists();

                foreach ($model->imageFiles as $index => $file) {
                    $fileName = uniqid() . '_' . $file->baseName . '.' . $file->extension;
                    $filePath = $uploadDir . $fileName;

                    if ($file->saveAs($filePath)) {
                        $imgModel = new PostImage();
                        $imgModel->post_id = $model->id;
                        $imgModel->path = 'uploads/posts/' . $fileName;
                        $imgModel->sort_order = $maxSortOrder + $index + 1;
                        $imgModel->is_main = (!$hasExistingImages && $index === 0) ? 1 : 0;
                        $imgModel->updated_at = date('Y-m-d H:i:s');
                        $imgModel->created_at = date('Y-m-d H:i:s');

                        if (!$imgModel->save()) {
                            Yii::error($imgModel->getErrors(), 'image_upload');
                            throw new \yii\web\ServerErrorHttpException('Greška pri spremanju slike posta u bazu: ' . json_encode($imgModel->getErrors()));
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
     * Briše pojedinačnu sliku posta putem AJAX-a i po potrebi postavlja novu glavnu sliku.
     */
    public function actionDeleteImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $image = PostImage::findOne($id);

            if ($image !== null) {
                $postId = $image->post_id;
                $wasMain = $image->is_main;

                $fullPath = Yii::getAlias('@webroot/') . $image->path;
                if (file_exists($fullPath) && is_file($fullPath)) {
                    unlink($fullPath);
                }

                if ($image->delete()) {
                    $newMainId = null;

                    // Ako je obrisana slika bila glavna (1), postavi prvu sljedeću slobodnu za ovaj post
                    if ($wasMain == 1) {
                        $nextImage = PostImage::find()
                            ->where(['post_id' => $postId])
                            ->orderBy(['sort_order' => SORT_ASC])
                            ->one();

                        if ($nextImage !== null) {
                            $nextImage->is_main = 1;
                            $nextImage->save(false);
                            $newMainId = $nextImage->id;
                        }
                    }

                    return [
                        'success' => true,
                        'message' => 'Slika posta je uspješno obrisana.',
                        'newMainId' => $newMainId
                    ];
                }
            }
        }

        return [
            'success' => false,
            'message' => 'Greška pri brisanju slike ili nevaljan zahtjev.'
        ];
    }

    /**
     * DOVRŠENO: Deletes an existing Post model and all its associated images.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Dohvaćanje svih slika povezanih s ovim postom
        $images = PostImage::find()->where(['post_id' => $model->id])->all();

        // Brisanje fizičkih datoteka s diska
        foreach ($images as $image) {
            $fullPath = Yii::getAlias('@webroot/') . $image->path;
            if (file_exists($fullPath) && is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        // Čišćenje zapisa slika iz baze za ovaj post
        PostImage::deleteAll(['post_id' => $model->id]);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * DODANO: Finds the Post model based on its primary key value.
     * Bez ove metode kontroler javlja grešku pri pretraživanju zapisa u bazi.
     * @param int $id ID
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
