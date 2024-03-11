<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_auto_comp}}`.
 */
class m230609_112722_create_partner_auto_comp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_auto_comp}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'partner_auto_model_id' => $this->integer(),
            'created_by_saas' => $this->boolean(),
            'created_by_car_price_bot' => $this->boolean(),
            'created_at' => $this->dateTime(),
        ]);

        $this->addColumn('partner_auto_brand', 'created_by_saas', $this->boolean());
        $this->addColumn('partner_auto_brand', 'created_by_car_price_bot', $this->boolean());

        $this->addColumn('partner_auto_model', 'created_by_saas', $this->boolean());
        $this->addColumn('partner_auto_model', 'created_by_car_price_bot', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_auto_comp}}');
    }
}
