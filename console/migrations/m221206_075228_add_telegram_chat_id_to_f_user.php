<?php

use yii\db\Migration;

/**
 * Class m221206_075228_add_telegram_chat_id_to_f_user
 */
class m221206_075228_add_telegram_chat_id_to_f_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'telegram_chat_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221206_075228_add_telegram_chat_id_to_f_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221206_075228_add_telegram_chat_id_to_f_user cannot be reverted.\n";

        return false;
    }
    */
}
