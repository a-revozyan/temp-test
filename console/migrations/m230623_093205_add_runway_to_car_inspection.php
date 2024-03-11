<?php

use yii\db\Migration;

/**
 * Class m230623_093205_add_runway_to_car_inspection
 */
class m230623_093205_add_runway_to_car_inspection extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'runway', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230623_093205_add_runway_to_car_inspection cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230623_093205_add_runway_to_car_inspection cannot be reverted.\n";

        return false;
    }
    */
}
