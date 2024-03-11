<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kapital_sugurta_request}}`.
 */
class m230505_045358_create_kapital_sugurta_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%kapital_sugurta_request}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->dateTime(),
            'osago_id' => $this->integer()->unsigned(),
            'travel_id' => $this->integer()->unsigned(),
            'accident_id' => $this->integer()->unsigned(),
            'token' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%kapital_sugurta_request}}');
    }
}
