<?php

use yii\db\Migration;

/**
 * Class m230727_080842_add_address_to_car_inspection_table
 */
class m230727_080842_add_address_to_car_inspection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'address', $this->string('500'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230727_080842_add_address_to_car_inspection_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230727_080842_add_address_to_car_inspection_table cannot be reverted.\n";

        return false;
    }
    */
}
