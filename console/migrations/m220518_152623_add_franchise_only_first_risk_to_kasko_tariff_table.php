<?php

use yii\db\Migration;

/**
 * Class m220518_152623_add_franchise_only_first_risk_to_kasko_tariff_table
 */
class m220518_152623_add_franchise_only_first_risk_to_kasko_tariff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_tariff', 'franchise_ru', $this->string(10485760));
        $this->addColumn('kasko_tariff', 'franchise_uz', $this->string(10485760));
        $this->addColumn('kasko_tariff', 'franchise_en', $this->string(10485760));
        $this->addColumn('kasko_tariff', 'only_first_risk_ru', $this->string(10485760));
        $this->addColumn('kasko_tariff', 'only_first_risk_en', $this->string(10485760));
        $this->addColumn('kasko_tariff', 'only_first_risk_uz', $this->string(10485760));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220518_152623_add_franchise_only_first_risk_to_kasko_tariff_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220518_152623_add_franchise_only_first_risk_to_kasko_tariff_table cannot be reverted.\n";

        return false;
    }
    */
}
