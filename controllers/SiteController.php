<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\Post;
use app\models\Product;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends AdminController
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        // Pronalazi samo poslednji uneti post (sortirano opadajuće po ID-u ili datumu)
        $post = Post::find()->orderBy(['id' => SORT_DESC])->one();
        $products = Product::find()->orderBy(['id' => SORT_DESC])->limit(3)->all(); // Prikazuje poslednjih 3 proizvoda

        // Ako u bazi uopšte nema postova, sprečavamo grešku
        if ($post === null) {
            $post = new Post([
                'title' => 'Dobrodošli',
                'content' => 'Trenutno nema objavljenih postova.'
            ]);
        }

        if (empty($products)) {
            $products = "<p>Trenutno nema objavljenih proizvoda.</p>";
        }

        return $this->render('index', [
            'model' => $post, // Prosleđujemo objekat
            'products' => $products, // Prosleđujemo listu proizvoda
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm($this->security);

        if ($model->load($this->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionProductView(int $id): string
    {
        $product = Product::findOne($id);

        if (!$product) {
            throw new \yii\web\NotFoundHttpException('Proizvod nije pronađen.');
        }

        return $this->render('product_view', ['product' => $product]);
    }

    public function actionAllProducts(): string
    {
        $request = Yii::$app->request;
        $search = $request->get('search');
        $stock = $request->get('stock');
        $sort = $request->get('sort');

        $query = Product::find();

        // 1. Pretraga po nazivu ili opisu
        if (!empty($search)) {
            $query->andFilterWhere([
                'or',
                ['like', 'name', $search],
                ['like', 'description', $search]
            ]);
        }

        // 2. Filter po stanju zalihe
        if ($stock === 'instock') {
            $query->andWhere(['>', 'stock', 0]);
        } elseif ($stock === 'outstock') {
            $query->andWhere(['<=', 'stock', 0]);
        }

        // 3. Sortiranje po cijeni ili zadano opadajuće po ID-u
        if ($sort === 'price_asc') {
            $query->orderBy(['price' => SORT_ASC]);
        } elseif ($sort === 'price_desc') {
            $query->orderBy(['price' => SORT_DESC]);
        } else {
            $query->orderBy(['id' => SORT_DESC]);
        }

        $products = $query->all();

        return $this->render('all_products', ['products' => $products]);
    }
}
