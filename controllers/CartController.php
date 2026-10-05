<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Product;
use app\models\OrderTable;
use app\models\OrderItemTable;

class CartController extends Controller
{
    // Prikaz košarice i forme za plaćanje pouzećem
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $cart = $session->get('cart', []);
        
        $products = [];
        $totalPrice = 0;

        // Dohvaćanje stvarnih proizvoda iz baze na temelju košarice
        if (!empty($cart)) {
            foreach ($cart as $productId => $quantity) {
                $product = Product::findOne($productId);
                if ($product) {
                    $products[] = [
                        'model' => $product,
                        'quantity' => $quantity,
                        'line_total' => $product->price * $quantity
                    ];
                    $totalPrice += $product->price * $quantity;
                }
            }
        }

        // Priprema modela narudžbe za formu
        $orderModel = new OrderTable();

        if ($this->request->isPost && !empty($cart)) {
            if ($orderModel->load($this->request->post())) {
                $orderModel->total_price = $totalPrice;
                $orderModel->payment_method = 'pouzece'; // Fiksno plaćanje pouzećem
                $orderModel->created_at = date('Y-m-d H:i:s');

                if ($orderModel->save()) {
                    
                    // KLJUČNI POPRAVAK 1: Osvježavamo model iz baze kako bismo dobili stvarni, novogenerirani ID narudžbe
                    $orderModel->refresh();

                    // Spremanje stavki u order_item_table
                    foreach ($products as $item) {
                        $orderItem = new OrderItemTable();
                        $orderItem->order_id = (int)$orderModel->id; // Osiguravamo cijeli broj
                        $orderItem->product_id = (int)$item['model']->id;
                        $orderItem->quantity = (int)$item['quantity'];
                        $orderItem->price = (float)$item['model']->price; // Osiguravamo decimalni broj

                        // KLJUČNI POPRAVAK 2: Hvatanje grešaka pri spremanju artikala
                        if (!$orderItem->save()) {
                            echo "<pre><strong>Yii2 Greška: Stavka narudžbe nije spremljena u bazu!</strong><br><br>";
                            print_r($orderItem->getErrors());
                            echo "<br>Podaci koje smo pokušali spremiti:<br>";
                            print_r($orderItem->attributes);
                            die(); // Zaustavljamo aplikaciju da vidimo točan razlog
                        }

                        // Smanjivanje zaliha ako u bazi imate stock stupac
                        if (isset($item['model']->stock)) {
                            $item['model']->stock -= $item['quantity'];
                            $item['model']->save(false);
                        }
                    }

                    // Pražnjenje košarice nakon što je sve 100% uspješno spremljeno
                    $session->remove('cart');
                    Yii::$app->session->setFlash('success', 'Vaša narudžba je zaprimljena! Plaćanje vršite pouzećem prilikom dostave.');
                    return $this->redirect(['site/index']);
                }
            }
        }

        return $this->render('index', [
            'products' => $products,
            'totalPrice' => $totalPrice,
            'orderModel' => $orderModel
        ]);
    }

    // Dodavanje u košaricu via POST
    public function actionAdd()
    {
        if ($this->request->isPost) {
            $productId = $this->request->post('product_id');
            $quantity = (int)$this->request->post('quantity', 1);

            $product = Product::findOne($productId);
            if (!$product) {
                throw new NotFoundHttpException('Proizvod ne postoji.');
            }

            $session = Yii::$app->session;
            $cart = $session->get('cart', []);

            if (isset($cart[$productId])) {
                $cart[$productId] += $quantity;
            } else {
                $cart[$productId] = $quantity;
            }

            $session->set('cart', $cart);
            Yii::$app->session->setFlash('success', "Proizvod {$product->name} je dodan u košaricu.");
            
            return $this->redirect(Yii::$app->request->referrer ?: ['cart/index']);
        }
    }

    // Uklanjanje iz košarice
    public function actionRemove($id)
    {
        $session = Yii::$app->session;
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);
            Yii::$app->session->setFlash('info', 'Proizvod je uklonjen iz košarice.');
        }

        return $this->redirect(['index']);
    }
}
