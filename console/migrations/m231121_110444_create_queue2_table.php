<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%queue2}}`.
 */
class m231121_110444_create_queue2_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%queue2}}', [
            'id' => $this->primaryKey(),
            'channel' => $this->string(255),
            'job' => $this->binary(),
            'pushed_at' => $this->integer(),
            'ttr' => $this->integer(),
            'delay' => $this->integer(),
            'priority' => $this->integer(),
            'reserved_at' => $this->integer(),
            'attempt' => $this->integer(),
            'done_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%queue2}}');
    }
}
