<?php

use yii\db\Migration;

/**
 * Class m220510_155249_add_time_of_sending_sms_to_f_user_table
 */
class m220510_155249_add_time_of_sending_sms_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("f_user", "time_of_sending_sms", $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220510_155249_add_time_of_sending_sms_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220510_155249_add_time_of_sending_sms_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
