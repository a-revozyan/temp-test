<?php

use yii\db\Migration;

/**
 * Class m221101_112949_add_min_price_max_price_to_kasko_tariff
 */
class m221101_112949_add_min_price_max_price_to_kasko_tariff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_tariff', 'min_price', $this->integer());
        $this->addColumn('kasko_tariff', 'max_price', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221101_112949_add_min_price_max_price_to_kasko_tariff cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221101_112949_add_min_price_max_price_to_kasko_tariff cannot be reverted.\n";

        return false;
    }
    */
}
