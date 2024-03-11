<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%gross_country}}`.
 */
class m230613_092618_create_gross_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%gross_country}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(5),
            'name_uz' => $this->string(),
            'name_ru' => $this->string(),
            'name_en' => $this->string(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%gross_country}}');
    }
}
