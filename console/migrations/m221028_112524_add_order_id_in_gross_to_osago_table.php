<?php

use yii\db\Migration;

/**
 * Class m221028_112524_add_order_id_in_gross_to_osago_table
 */
class m221028_112524_add_order_id_in_gross_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'order_id_in_gross', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221028_112524_add_order_id_in_gross_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221028_112524_add_order_id_in_gross_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
