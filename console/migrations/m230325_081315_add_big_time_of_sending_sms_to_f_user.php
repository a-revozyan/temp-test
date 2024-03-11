<?php

use yii\db\Migration;

/**
 * Class m230325_081315_add_big_time_of_sending_sms_to_f_user
 */
class m230325_081315_add_big_time_of_sending_sms_to_f_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("f_user", "big_time_of_sending_sms", $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230325_081315_add_big_time_of_sending_sms_to_f_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230325_081315_add_big_time_of_sending_sms_to_f_user cannot be reverted.\n";

        return false;
    }
    */
}
