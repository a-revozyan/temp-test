<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%unique_code}}`.
 */
class m230214_071041_create_unique_code_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%unique_code}}', [
            'id' => $this->primaryKey(),
            'clonable_id' => $this->integer(),
            'code' => $this->string(),
            'discount_percent' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%unique_code}}');
    }
}
