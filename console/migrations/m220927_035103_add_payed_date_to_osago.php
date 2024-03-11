<?php

use yii\db\Migration;

/**
 * Class m220927_035103_add_payed_date_to_osago
 */
class m220927_035103_add_payed_date_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'payed_date', $this->integer());
        $this->addColumn('osago', 'policy_pdf_url', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220927_035103_add_payed_date_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220927_035103_add_payed_date_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
