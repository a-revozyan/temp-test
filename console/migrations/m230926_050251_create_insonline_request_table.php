<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%insonline_request}}`.
 */
class m230926_050251_create_insonline_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%insonline_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->dateTime(),
            'osago_id' => $this->integer()->unsigned(),
            'accident_id' => $this->integer()->unsigned(),
            'token' => $this->string(),
            'taken_time' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%insonline_request}}');
    }
}
