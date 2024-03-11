<?php

use yii\db\Migration;

/**
 * Class m230113_135542_add_accident_amount_to_osago_table
 */
class m230113_135542_add_accident_amount_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'accident_amount', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230113_135542_add_accident_amount_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230113_135542_add_accident_amount_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
