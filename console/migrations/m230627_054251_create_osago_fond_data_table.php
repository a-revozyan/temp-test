<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%osago_fond_data}}`.
 */
class m230627_054251_create_osago_fond_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%osago_fond_data}}', [
            'id' => $this->primaryKey(),
            'osago_id' => $this->integer(),
            'marka_id' => $this->integer(),
            'model_id' => $this->integer(),
            'model_name' => $this->string(),
            'vehicle_type_id' => $this->integer(),
            'tech_passport_issue_date' => $this->string(),
            'issue_year' => $this->integer(),
            'body_number' => $this->string(),
            'engine_number' => $this->string(),
            'use_territory' => $this->integer(),
            'fy' => $this->integer(),
            'last_name_latin' => $this->string(),
            'first_name_latin' => $this->string(),
            'middle_name_latin' => $this->string(),
            'oblast' => $this->integer(),
            'rayon' => $this->integer(),
            'ispensioner' => $this->integer(),
            'orgname' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%osago_fond_data}}');
    }
}
