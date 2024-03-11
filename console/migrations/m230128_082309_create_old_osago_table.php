<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%old_osago}}`.
 */
class m230128_082309_create_old_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%old_osago}}', [
            'id' => $this->primaryKey(),
            'old_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'insurer_name' => $this->string(),
            'policy_number' => $this->string(),
            'insurer_phone_number' => $this->string(),
            'owner' => $this->string(),
            'amount_uzs' => $this->integer(),
            'status' => $this->integer(),
            'payment_type' => $this->string(),
            'imported_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%old_osago}}');
    }
}
