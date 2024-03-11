<?php

use yii\db\Migration;

/**
 * Class m220416_073632_warehouse_status
 */
class m220416_073632_warehouse_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('warehouse', 'status', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220416_073632_warehouse_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220416_073632_warehouse_status cannot be reverted.\n";

        return false;
    }
    */
}
