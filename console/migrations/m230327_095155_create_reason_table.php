<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reason}}`.
 */
class m230327_095155_create_reason_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%reason}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(10485760),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%reason}}');
    }
}
