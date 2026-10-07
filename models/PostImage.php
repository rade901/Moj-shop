<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post_image".
 *
 * @property int $id
 * @property int $post_id
 * @property string $path
 * @property int $is_main
 * @property int $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Post $post
 */
class PostImage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort_order'], 'default', 'value' => 0],
            [['post_id', 'path', 'created_at', 'updated_at'], 'required'],
            [['post_id', 'is_main', 'sort_order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['path'], 'string', 'max' => 255],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::class, 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'path' => Yii::t('app', 'Path'),
            'is_main' => Yii::t('app', 'Is Main'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

}
