<?php

use yii\db\Migration;

/**
 * Class m221215_114632_make_columns_of_warehouse_nullable
 */
class m221215_114632_make_columns_of_warehouse_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('warehouse', 'partner_id', $this->integer()->null());
        $this->alterColumn('warehouse', 'product_id', $this->integer()->null());
        $this->alterColumn('warehouse', 'series', $this->string()->null());
        $this->alterColumn('warehouse', 'number', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221215_114632_make_columns_of_warehouse_nullable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221215_114632_make_columns_of_warehouse_nullable cannot be reverted.\n";

        return false;
    }
    */
}
