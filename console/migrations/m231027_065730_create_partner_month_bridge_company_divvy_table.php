<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partner_month_bridge_company_divvy}}`.
 */
class m231027_065730_create_partner_month_bridge_company_divvy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner_month_bridge_company_divvy}}', [
            'id' => $this->primaryKey(),
            'bridge_company_id' => $this->integer(),
            'partner_id' => $this->integer(),
            'product_id' => $this->integer(),
            'number_drivers_id' => $this->integer(),
            'month' => $this->string(),
            'percent' => $this->float(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%partner_month_bridge_company_divvy}}');
    }
}
