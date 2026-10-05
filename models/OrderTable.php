<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


/**
 * This is the model class for table "order_table".
 *
 * @property int $id
 * @property string $customer_name
 * @property string $customer_lastname
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string|null $status
 * @property string $post_code
 * @property string|null $payment_method
 * @property float $total_price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OrderItemTable[] $orderItemTables
 */
class OrderTable extends \yii\db\ActiveRecord
{


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                // Budući da vaša baza koristi DATETIME format ('Y-m-d H:i:s'), 
                // govorimo Yii-ju da koristi SQL izraz NOW() umjesto običnog timestampa
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_table';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 'pending'],
            [['payment_method'], 'default', 'value' => 'pouzece'],

            // KORISNIČKA FORMA: Samo ova polja kupac zapravo šalje kroz formu i ona su obavezna
            [['customer_name', 'email', 'phone', 'address'], 'required'],

            // SIGURNA I AUTOMATSKA POLJA: Maknuta su iz 'required' kako validacija ne bi pucala
            [['address'], 'string'],
            [['total_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],

            // Dozvoljavamo da prezime i poštanski broj budu prazni (ili opcionalni) jer ih nemamo u formi
            [['customer_name', 'customer_lastname', 'email'], 'string', 'max' => 255],
            [['phone', 'status', 'payment_method'], 'string', 'max' => 50],
            [['post_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_name' => Yii::t('app', 'Customer name'),
            'customer_lastname' => Yii::t('app', 'Customer Lastname'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'address' => Yii::t('app', 'Address'),
            'status' => Yii::t('app', 'Status'),
            'post_code' => Yii::t('app', 'Post Code'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'total_price' => Yii::t('app', 'Total Price'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[OrderItemTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItemTables()
    {
        return $this->hasMany(OrderItemTable::class, ['order_id' => 'id']);
    }

    /**
     * Popravljena relacija prema stavkama narudžbe
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        // Povezujemo se s modelom stavki preko ispravnog order_id-a
        return $this->hasMany(OrderItemTable::class, ['order_id' => 'id']);
    }

    // status 

    public function getStatusLabel()
    {
        switch ($this->status) {
            case 'pending':
                return 'čekanje';
            case 'completed':
                return 'završeno';
            case 'shipping':
                return 'dostava';
            case 'sent':
                return 'poslano';
            default:
                return $this->status; // Ako je status nepoznat, vratimo ga onako kako jest
        }
    }
}
