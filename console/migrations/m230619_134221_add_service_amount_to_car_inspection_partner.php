<?php

use yii\db\Migration;

/**
 * Class m230619_134221_add_service_amount_to_car_inspection_partner
 */
class m230619_134221_add_service_amount_to_car_inspection_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('car_inspection', 'service_amount', $this->integer());
        $this->addColumn('partner', 'service_amount', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230619_134221_add_service_amount_to_car_inspection_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230619_134221_add_service_amount_to_car_inspection_partner cannot be reverted.\n";

        return false;
    }
    */
}
