<?php

use yii\db\Migration;

/**
 * Class m220802_122023_old_kasko_risk
 */
class m220802_122023_old_kasko_risk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('old_kasko_risk', [
            'id' => $this->primaryKey(),
            'kasko_risk_id' => $this->integer(),
            'name_ru' => $this->string(255),
            'name_uz' => $this->string(255),
            'name_en' => $this->string(255),
            'category_id' => $this->integer(),
            'amount' => $this->double(),
            'description_ru' => $this->string(255),
            'description_en' => $this->string(255),
            'description_uz' => $this->string(255),
            'show_desc' => $this->integer(),
            'tariff_id' => $this->integer(),
            'tariff_partner_id' => $this->integer(),
            'tariff_name' => $this->string(255),
            'tariff_amount_kind' => $this->string(255),
            'tariff_amount' => $this->double(),
            'tariff_franchise_ru' => $this->string(10485760),
            'tariff_franchise_uz' => $this->string(10485760),
            'tariff_franchise_en' => $this->string(10485760),
            'tariff_only_first_risk_ru' => $this->string(10485760),
            'tariff_only_first_risk_uz' => $this->string(10485760),
            'tariff_only_first_risk_en' => $this->string(10485760),
            'tariff_is_conditional' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220802_122023_old_kasko_risk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220802_122023_old_kasko_risk cannot be reverted.\n";

        return false;
    }
    */
}
