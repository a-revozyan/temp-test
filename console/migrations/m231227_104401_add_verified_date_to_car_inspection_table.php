<?php

use yii\db\Migration;

/**
 * Class m231227_104401_add_verified_date_to_car_inspection_table
 */
class m231227_104401_add_verified_date_to_car_inspection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'verified_date', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231227_104401_add_verified_date_to_car_inspection_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231227_104401_add_verified_date_to_car_inspection_table cannot be reverted.\n";

        return false;
    }
    */
}
