<?php

use yii\db\Migration;

/**
 * Class m220519_153155_add_is_conditional_to_kasko_tariff_table
 */
class m220519_153155_add_is_conditional_to_kasko_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_tariff', 'is_conditional', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220519_153155_add_is_conditional_to_kasko_tariff_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220519_153155_add_is_conditional_to_kasko_tariff_table cannot be reverted.\n";

        return false;
    }
    */
}
