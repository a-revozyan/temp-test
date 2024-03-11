<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bridge_company}}`.
 */
class m220511_150948_create_bridge_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bridge_company}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'code' => $this->string()->unique(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bridge_company}}');
    }
}
