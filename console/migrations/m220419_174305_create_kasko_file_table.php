<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kasko_file}}`.
 */
class m220419_174305_create_kasko_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%kasko_file}}', [
            'id' => $this->primaryKey(),
            'kasko_id' => $this->integer(),
            'path' => $this->string(),
            'type' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-kasko_file-kasko_id',
            'kasko_file',
            'kasko_id',
            'kasko',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%kasko_file}}');
    }
}
