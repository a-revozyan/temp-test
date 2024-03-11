<?php

use yii\db\Migration;

/**
 * Class m220629_153115_add_sum_insured_to_travel_partner_extra_insurance_table
 */
class m220629_153115_add_sum_insured_to_travel_partner_extra_insurance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel_partner_extra_insurance', 'sum_insured', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_153115_add_sum_insured_to_travel_partner_extra_insurance_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220629_153115_add_sum_insured_to_travel_partner_extra_insurance_table cannot be reverted.\n";

        return false;
    }
    */
}
