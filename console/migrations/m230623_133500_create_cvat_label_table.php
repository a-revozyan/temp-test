<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cvat_label}}`.
 */
class m230623_133500_create_cvat_label_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cvat_label}}', [
            'id' => $this->primaryKey(),
            'label_id' => $this->integer(),
            'name' => $this->string(),
            'color' => $this->string(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cvat_label}}');
    }
}
