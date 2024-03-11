<?php

use yii\db\Migration;

/**
 * Class m230731_093508_add_year_to_car_inspection_table
 */
class m230731_093508_add_year_to_car_inspection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'year', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230731_093508_add_year_to_car_inspection_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230731_093508_add_year_to_car_inspection_table cannot be reverted.\n";

        return false;
    }
    */
}
