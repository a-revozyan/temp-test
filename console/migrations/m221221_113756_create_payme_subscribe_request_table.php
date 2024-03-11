<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payme_subscribe_request}}`.
 */
class m221221_113756_create_payme_subscribe_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payme_subscribe_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->timestamp(),
            'model_id' => $this->integer(),
            'model_class' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payme_subscribe_request}}');
    }
}
