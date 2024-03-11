<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kasko_by_subscription_policy}}`.
 */
class m230121_051739_create_kasko_by_subscription_policy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%kasko_by_subscription_policy}}', [
            'id' => $this->primaryKey(),
            'kasko_by_subscription_id' => $this->integer(),
            'policy_number' => $this->string(),
            'policy_pdf_url' => $this->string(),
            'begin_date' => $this->dateTime(),
            'end_date' => $this->dateTime(),
            'trans_id' => $this->integer(),
            'saved_card_id' => $this->integer(),
            'status' => $this->integer(),
            'order_id_in_gross' => $this->integer(),
            'partner_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'payed_date' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%kasko_by_subscription_policy}}');
    }
}
