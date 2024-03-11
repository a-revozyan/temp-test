<?php

use yii\db\Migration;

/**
 * Class m230105_105706_alter_accident_table
 */
class m230105_105706_alter_accident_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('accident', 'insurer_name', $this->string());
        $this->alterColumn('accident', 'amount_usd', $this->double());
        $this->alterColumn('accident', 'amount_uzs', $this->double());
        $this->alterColumn('accident', 'viewed', $this->boolean());
        $this->alterColumn('accident', 'insurer_birthday', $this->date());
        $this->addColumn('accident', 'policy_pdf_url', $this->string());
        $this->addColumn('accident', 'order_id_in_gross', $this->integer());
        $this->addColumn('accident', 'f_user_id', $this->integer());
        $this->addColumn('accident', 'osago_id', $this->integer());
        $this->addColumn('accident', 'payed_date', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230105_105706_alter_accident_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230105_105706_alter_accident_table cannot be reverted.\n";

        return false;
    }
    */
}
