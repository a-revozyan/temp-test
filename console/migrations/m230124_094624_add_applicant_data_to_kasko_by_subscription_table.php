<?php

use yii\db\Migration;

/**
 * Class m230124_094624_add_applicant_data_to_kasko_by_subscription_table
 */
class m230124_094624_add_applicant_data_to_kasko_by_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription', 'applicant_name', $this->string());
        $this->addColumn('kasko_by_subscription', 'applicant_pass_series', $this->string());
        $this->addColumn('kasko_by_subscription', 'applicant_pass_number', $this->string());
        $this->addColumn('kasko_by_subscription', 'applicant_birthday', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230124_094624_add_applicant_data_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230124_094624_add_applicant_data_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }
    */
}
