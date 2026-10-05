<?php

use yii\db\Migration;

class m261002_130000_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Kreiranje glavne tablice 'product' (izbacili smo polje 'image')
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'price' => $this->decimal(10, 2)->notNull(),
            'stock' => $this->integer()->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        // 2. Kreiranje tablice 'product_image' za spremanje više slika
        $this->createTable('{{%product_image}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(), // Veza prema proizvodu
            'path' => $this->string()->notNull(),        // Putanja do datoteke slike
            'is_main' => $this->boolean()->notNull()->defaultValue(false), // Je li ovo glavna slika na popisu?
            'sort_order' => $this->integer()->notNull()->defaultValue(0),  // Redoslijed prikazivanja slika
            'created_at' => $this->dateTime()->notNull(),
        ]);

        // 3. Dodavanje indeksa za brže pretraživanje slika po ID-u proizvoda
        $this->createIndex(
            '{{%idx-product_image-product_id}}',
            '{{%product_image}}',
            'product_id'
        );

        // 4. Dodavanje stranog ključa (Foreign Key) - ako se obriše proizvod, brišu se i njegove slike u bazi
        $this->addForeignKey(
            '{{%fk-product_image-product_id}}',
            '{{%product_image}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Prvo brišemo tablicu sa stranim ključem, pa onda glavnu tablicu
        $this->dropForeignKey('{{%fk-product_image-product_id}}', '{{%product_image}}');
        $this->dropTable('{{%product_image}}');
        $this->dropTable('{{%product}}');
    }
}
