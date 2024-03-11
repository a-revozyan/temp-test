<?php

use yii\db\Migration;

/**
 * Class m221222_125524_add_sms_template_id_to_sms_history_table
 */
class m221222_125524_add_sms_template_id_to_sms_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sms_history', 'sms_template_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221222_125524_add_sms_template_id_to_sms_history_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221222_125524_add_sms_template_id_to_sms_history_table cannot be reverted.\n";

        return false;
    }
    */
}
