<?php

use yii\db\Migration;

/**
 * Class m230204_061911_alter_telegram_chat_id_from_sms_history_table
 */
class m230204_061911_alter_telegram_chat_id_from_sms_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('sms_history', 'telegram_chat_id', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230204_061911_alter_telegram_chat_id_from_sms_history_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230204_061911_alter_telegram_chat_id_from_sms_history_table cannot be reverted.\n";

        return false;
    }
    */
}
