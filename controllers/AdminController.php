<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class AdminController extends Controller
{
    public function beforeAction($action)
{
    // Pozivamo roditeljsku metodu da sve ostalo radi normalno
    if (!parent::beforeAction($action)) {
        return false;
    }

    // Proveravamo da li je korisnik ulogovan (nije gost)
    if (!Yii::$app->user->isGuest) {
        // Ako je ulogovan, koristi admin layout
        $this->layout = 'admin';
    } else {
        // Ako je gost, koristi običan (main) layout
        $this->layout = 'main';
    }

    return true;
}

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Dozvoljeno samo ulogovanim korisnicima
                    ],
                ],
            ],
        ];
    }

    /**
     * Admin Dashboard početna strana
     * Ruta: /index.php?r=admin/index
     */
    public function actionIndex()
    {
        // Ovde možeš da povučeš statistiku iz baze (npr. ukupan broj postova, korisnika, itd.)
        $totalPosts = \app\models\Post::find()->count();

        return $this->render('index', [
            'totalPosts' => $totalPosts,
        ]);
    }
}