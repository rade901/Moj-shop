<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $category_id
 *
 * @property Category $category
 * @property PostImage[] $images
 */
class Post extends \yii\db\ActiveRecord
{
    // Virtualno polje za prihvaćanje više datoteka iz forme
    public $imageFiles;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id'], 'default', 'value' => null],
            // POPRAVLJENO: Izbačeni 'created_at' i 'updated_at' iz 'required'
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'], // Ovdje ostaju, što je u redu
            [['category_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 10],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Naslov'),
            'content' => Yii::t('app', 'Sadržaj'),
            'created_at' => Yii::t('app', 'Kreirano'),
            'updated_at' => Yii::t('app', 'Ažurirano'),
            'category_id' => Yii::t('app', 'Kategorija'),
            'imageFiles' => Yii::t('app', 'Slike članka'),
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * PROMIJENJENO: Naziv relacije preimenovan iz getPostImages u getImages
     * Sada se savršeno podudara s pozivom $model->images u formi i kontroleru.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(PostImage::class, ['post_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }
}
