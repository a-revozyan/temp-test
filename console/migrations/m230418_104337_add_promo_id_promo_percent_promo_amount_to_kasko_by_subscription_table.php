<?php

use yii\db\Migration;

/**
 * Class m230418_104337_add_promo_id_promo_percent_promo_amount_to_kasko_by_subscription_table
 */
class m230418_104337_add_promo_id_promo_percent_promo_amount_to_kasko_by_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription', 'promo_id', $this->integer());
        $this->addColumn('kasko_by_subscription', 'promo_percent', $this->double());
        $this->addColumn('kasko_by_subscription', 'promo_amount', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230418_104337_add_promo_id_promo_percent_promo_amount_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230418_104337_add_promo_id_promo_percent_promo_amount_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }
    */
}
