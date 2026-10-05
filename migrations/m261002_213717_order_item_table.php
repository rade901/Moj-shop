<?php

use yii\db\Migration;

class m261002_213717_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_item_table}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull(),
            'price' => $this->decimal(10,2)->notNull(),
        ]);

        // Add foreign key for order_id
        $this->addForeignKey(
            'fk_order_item_order',
            '{{%order_item_table}}',
            'order_id',
            '{{%order_table}}',
            'id',
            'CASCADE'
        );

        // Add foreign key for product_id
        $this->addForeignKey(
            'fk_order_item_product',
            '{{%order_item_table}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk_order_item_order', '{{%order_item_table}}');
        $this->dropForeignKey('fk_order_item_product', '{{%order_item_table}}');

        // Then drop the table
        $this->dropTable('{{%order_item_table}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261002_213717_order_item_table cannot be reverted.\n";

        return false;
    }
    */
}
