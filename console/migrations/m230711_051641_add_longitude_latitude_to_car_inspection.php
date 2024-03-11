<?php

use yii\db\Migration;

/**
 * Class m230711_051641_add_longitude_latitude_to_car_inspection
 */
class m230711_051641_add_longitude_latitude_to_car_inspection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'longitude', $this->double());
        $this->addColumn('car_inspection', 'latitude', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230711_051641_add_longitude_latitude_to_car_inspection cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230711_051641_add_longitude_latitude_to_car_inspection cannot be reverted.\n";

        return false;
    }
    */
}
