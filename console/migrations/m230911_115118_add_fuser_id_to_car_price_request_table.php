<?php

use yii\db\Migration;

/**
 * Class m230911_115118_add_fuser_id_to_car_price_request_table
 */
class m230911_115118_add_fuser_id_to_car_price_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_price_request', 'fuser_id', $this->integer()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230911_115118_add_fuser_id_to_car_price_request_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230911_115118_add_fuser_id_to_car_price_request_table cannot be reverted.\n";

        return false;
    }
    */
}
