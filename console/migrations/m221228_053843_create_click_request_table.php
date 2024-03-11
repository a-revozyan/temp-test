<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%click_request}}`.
 */
class m221228_053843_create_click_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%click_request}}', [
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
        $this->dropTable('{{%click_request}}');
    }
}
