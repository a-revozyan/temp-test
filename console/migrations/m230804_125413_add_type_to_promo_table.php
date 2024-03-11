<?php

use yii\db\Migration;

/**
 * Class m230804_125413_add_type_to_promo_table
 */
class m230804_125413_add_type_to_promo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('promo', 'type', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230804_125413_add_type_to_promo_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230804_125413_add_type_to_promo_table cannot be reverted.\n";

        return false;
    }
    */
}
