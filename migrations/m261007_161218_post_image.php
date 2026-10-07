<?php

use yii\db\Migration;

class m261007_161218_post_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%post_image}}', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(), // Veza prema postu
            'path' => $this->string()->notNull(),        // Putanja do datoteke slike
            'is_main' => $this->boolean()->notNull()->defaultValue(false), // Je li ovo glavna slika na popisu?
            'sort_order' => $this->integer()->notNull()->defaultValue(0),  // Redoslijed prikazivanja slika
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        // creates index for column `post_id`
        $this->createIndex(
            '{{%idx-post_image-post_id}}',
            '{{%post_image}}',
            'post_id'
        );

        // add foreign key for table `{{%post}}`
        $this->addForeignKey(
            '{{%fk-post_image-post_id}}',
            '{{%post_image}}',
            'post_id',
            '{{%post}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-post_image-post_id}}',
            '{{%post_image}}'
        );

        $this->dropTable('{{%post_image}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261007_161218_post_image cannot be reverted.\n";

        return false;
    }
    */
}
