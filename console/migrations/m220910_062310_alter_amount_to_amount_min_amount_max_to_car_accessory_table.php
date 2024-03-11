<?php

use yii\db\Migration;

/**
 * Class m220910_062310_alter_amount_to_amount_min_amount_max_to_car_accessory_table
 */
class m220910_062310_alter_amount_to_amount_min_amount_max_to_car_accessory_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('car_accessory', 'amount', 'amount_min');
        $this->addColumn('car_accessory', 'amount_max', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220910_062310_alter_amount_to_amount_min_amount_max_to_car_accessory_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220910_062310_alter_amount_to_amount_min_amount_max_to_car_accessory_table cannot be reverted.\n";

        return false;
    }
    */
}
