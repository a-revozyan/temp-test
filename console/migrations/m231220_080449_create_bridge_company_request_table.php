<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bridge_company_request}}`.
 */
class m231220_080449_create_bridge_company_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bridge_company_request}}', [
            'id' => $this->primaryKey(),
            'bridge_company_id' => $this->integer(),
            'url' => $this->string(),
            'request_body' => $this->text(),
            'response_body' => $this->text(),
            'send_date' => $this->dateTime(),
            'taken_time' => $this->integer(),
            'osago_id' => $this->integer(),
            'accident_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bridge_company_request}}');
    }
}
