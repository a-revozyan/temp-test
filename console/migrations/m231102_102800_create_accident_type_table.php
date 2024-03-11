<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%accident_type}}`.
 */
class m231102_102800_create_accident_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%accident_type}}', [
            'id' => $this->primaryKey(),
            'name_uz' => $this->string(),
            'name_ru' => $this->string(),
            'name_en' => $this->string(),
            'description_en' => $this->string(),
            'description_ru' => $this->string(),
            'description_uz' => $this->string(),
            'required' => $this->integer(),
        ]);

        $this->addColumn('partner', 'accident_type_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%accident_type}}');
    }
}
