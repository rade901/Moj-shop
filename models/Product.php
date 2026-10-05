<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

class Product extends \yii\db\ActiveRecord
{
    /**
     * DODANO: Eksplicitno definiranje naziva tablice u bazi
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    public function attributeLabels()
{
    return [
        'id' => 'ID',
        'name' => 'Naziv proizvoda',         // Promijenjeno s 'Name'
        'description' => 'Opis',            // Promijenjeno s 'Description'
        'price' => 'Cijena (EUR)',          // Promijenjeno s 'Price'
        'stock' => 'Količina na skladištu',  // Promijenjeno s 'Stock'
        'is_active' => 'Proizvod je aktivan',// Promijenjeno s 'Is Active'
        'imageFiles' => 'Odaberite slike',   // Naziv za polje uploada slika
        'created_at' => 'Datum kreiranja',
        'updated_at' => 'Zadnja izmjena',
        'views_count' => 'Broj pregleda',    // Dodano za prikaz broja pregleda
    ];
}


    /**
     * @var UploadedFile[] Privremeno svojstvo za prihvat slika iz forme
     */
    public $imageFiles;

    public function rules()
    {
        return [
            [['name', 'price', 'stock'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['stock', 'views_count'], 'integer'],
            [['is_active'], 'boolean'],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxFiles' => 10, 'maxSize' => 1024 * 1024 * 5],
        ];
    }

    /**
     * Aktivira se automatski prije nego što se proizvod obriše iz baze.
     * Briše fizičke datoteke slika s diska servera.
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        foreach ($this->images as $image) {
            $fullPath = Yii::getAlias('@webroot/') . $image->path;
            if (file_exists($fullPath) && is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        return true;
    }

    /**
     * Veza prema tablici sa slikama
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id']);
    }
}
