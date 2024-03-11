<?php

use yii\db\Migration;

/**
 * Class m221028_123238_add_begin_date_end_date_change_policy_pdf_url_integer_in_osago
 */
class m221028_123238_add_begin_date_end_date_change_policy_pdf_url_integer_in_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('osago', 'policy_pdf_url', $this->string());
        $this->addColumn('osago', 'begin_date', $this->date());
        $this->addColumn('osago', 'end_date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221028_123238_add_begin_date_end_date_change_policy_pdf_url_integer_in_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221028_123238_add_begin_date_end_date_change_policy_pdf_url_integer_in_osago cannot be reverted.\n";

        return false;
    }
    */
}
