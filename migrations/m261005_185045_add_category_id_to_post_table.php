<?php

use yii\db\Migration;

class m261005_185045_add_category_id_to_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%post}}', 'category_id', $this->integer()->null());

        // Add foreign key for category_id
        $this->addForeignKey(
            'fk_post_category',
            '{{%post}}',
            'category_id',
            '{{%category}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_post_category', '{{%post}}');
        $this->dropColumn('{{%post}}', 'category_id');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261005_185045_add_category_id_to_post_table cannot be reverted.\n";

        return false;
    }
    */
}
