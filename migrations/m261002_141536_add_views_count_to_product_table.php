<?php

use yii\db\Migration;

class m261002_141536_add_views_count_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Dodavanje stupca 'views_count' u tablicu 'product'
        $this->addColumn('{{%product}}', 'views_count', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('{{%product}}', 'views_count');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261002_141536_add_views_count_to_product_table cannot be reverted.\n";

        return false;
    }
    */
}
