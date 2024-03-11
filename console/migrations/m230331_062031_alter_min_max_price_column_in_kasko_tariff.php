<?php

use yii\db\Migration;

/**
 * Class m230331_062031_alter_min_max_price_column_in_kasko_tariff
 */
class m230331_062031_alter_min_max_price_column_in_kasko_tariff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('kasko_tariff', 'min_price', $this->bigInteger());
        $this->alterColumn('kasko_tariff', 'max_price', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230331_062031_alter_min_max_price_column_in_kasko_tariff cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230331_062031_alter_min_max_price_column_in_kasko_tariff cannot be reverted.\n";

        return false;
    }
    */
}
