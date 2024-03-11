<?php

use yii\db\Migration;

/**
 * Class m221129_130220_add_min_price_max_price_to_old_kasko_risk
 */
class m221129_130220_add_min_price_max_price_to_old_kasko_risk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('old_kasko_risk', 'tariff_min_price', $this->integer());
        $this->addColumn('old_kasko_risk', 'tariff_max_price', $this->integer());
        $this->addColumn('old_kasko_risk', 'tariff_min_year', $this->integer());
        $this->addColumn('old_kasko_risk', 'tariff_max_year', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_130220_add_min_price_max_price_to_old_kasko_risk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_130220_add_min_price_max_price_to_old_kasko_risk cannot be reverted.\n";

        return false;
    }
    */
}
