<?php

use yii\db\Migration;

/**
 * Class m221128_112223_add_sent_sms_count_to_f_user
 */
class m221128_112223_add_sent_sms_count_to_f_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'sent_sms_count', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221128_112223_add_sent_sms_count_to_f_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221128_112223_add_sent_sms_count_to_f_user cannot be reverted.\n";

        return false;
    }
    */
}
