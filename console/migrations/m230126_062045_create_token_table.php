<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%token}}`.
 */
class m230126_062045_create_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%token}}', [
            'id' => $this->primaryKey(),
            'f_user_id' => $this->integer(),
            'verification_token' => $this->string(),
            'access_token' => $this->string(),
            'telegram_chat_id' => $this->bigInteger(),
            'telegram_lang' => $this->string(),
            'status' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->dropColumn('f_user', 'auth_key');
        $this->dropColumn('f_user', 'password_reset_token');
        $this->dropColumn('f_user', 'verification_token');
        $this->dropColumn('f_user', 'access_token');
        $this->dropColumn('f_user', 'telegram_chat_id');
        $this->dropColumn('f_user', 'telegram_lang');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%token}}');
    }
}
