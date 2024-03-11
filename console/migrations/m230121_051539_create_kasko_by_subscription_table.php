<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kasko_by_subscription}}`.
 */
class m230121_051539_create_kasko_by_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%kasko_by_subscription}}', [
            'id' => $this->primaryKey(),
            'f_user_id' => $this->integer(),
            'program_id' => $this->integer(),
            'calc_type' => $this->string(3),
            'count' => $this->integer(),
            'amount_uzs' => $this->integer(),
            'amount_avto' => $this->integer(),
            'autonumber' => $this->string(),
            'tech_pass_series' => $this->string(),
            'tech_pass_number' => $this->string(),
            'status' => $this->integer(),
            "created_at" => $this->dateTime(),
            'saved_card_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%kasko_by_subscription}}');
    }
}
