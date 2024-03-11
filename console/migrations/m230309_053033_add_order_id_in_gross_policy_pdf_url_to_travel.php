<?php

use yii\db\Migration;

/**
 * Class m230309_053033_add_order_id_in_gross_policy_pdf_url_to_travel
 */
class m230309_053033_add_order_id_in_gross_policy_pdf_url_to_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'order_id_in_gross', $this->integer());
        $this->addColumn('travel','policy_pdf_url', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230309_053033_add_order_id_in_gross_policy_pdf_url_to_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230309_053033_add_order_id_in_gross_policy_pdf_url_to_travel cannot be reverted.\n";

        return false;
    }
    */
}
