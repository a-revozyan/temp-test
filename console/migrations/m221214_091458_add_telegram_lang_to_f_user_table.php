<?php

use yii\db\Migration;

/**
 * Class m221214_091458_add_telegram_lang_to_f_user_table
 */
class m221214_091458_add_telegram_lang_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'telegram_lang', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221214_091458_add_telegram_lang_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221214_091458_add_telegram_lang_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
