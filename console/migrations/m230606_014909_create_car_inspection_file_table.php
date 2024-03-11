<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_insurance_files}}`.
 */
class m230606_014909_create_car_inspection_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_inspection_file}}', [
            'id' => $this->primaryKey(),
            'car_inspection_id' => $this->integer(),
            'url' => $this->string(),
            'type' => $this->smallInteger(),
            'status' => $this->smallInteger(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%car_insurance_files}}');
    }
}
