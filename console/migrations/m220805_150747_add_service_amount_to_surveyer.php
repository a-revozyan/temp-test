<?php

use yii\db\Migration;

/**
 * Class m220805_150747_add_service_amount_to_surveyer
 */
class m220805_150747_add_service_amount_to_surveyer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'service_amount', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220805_150747_add_service_amount_to_surveyer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220805_150747_add_service_amount_to_surveyer cannot be reverted.\n";

        return false;
    }
    */
}
