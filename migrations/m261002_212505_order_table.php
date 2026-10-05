<?php

use yii\db\Migration;

class m261002_212505_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_table}}', [
            'id' => $this->primaryKey(),
            'customer_name' => $this->string(255)->notNull(),
            'customer_lastname' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'phone' => $this->string(50)->notNull(),
            'address' => $this->text()->notNull(),
            'status' => $this->string(50)->defaultValue('pending'),
            'post_code' => $this->string(20)->notNull(),
            'payment_method' => $this->string(50)->defaultValue('pouzece'),
            'total_price' => $this->decimal(10,2)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_table}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261002_212505_order_table cannot be reverted.\n";

        return false;
    }
    */
}
