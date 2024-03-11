<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%gross_auto}}`.
 */
class m230427_033557_create_gross_auto_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%gross_auto}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%gross_auto}}');
    }
}
