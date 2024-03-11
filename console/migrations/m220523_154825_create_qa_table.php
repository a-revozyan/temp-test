<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%qa}}`.
 */
class m220523_154825_create_qa_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%qa}}', [
            'id' => $this->primaryKey(),
            'question_uz' => $this->string(65535),
            'question_en' => $this->string(65535),
            'question_ru' => $this->string(65535),
            'answer_uz' => $this->string(65535),
            'answer_en' => $this->string(65535),
            'answer_ru' => $this->string(65535),
            'status' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%qa}}');
    }
}
