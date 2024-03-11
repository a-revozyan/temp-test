<?php

use yii\db\Migration;

/**
 * Class m231201_052343_add_bridge_company_id_and_partner_id_to_kasko_by_subscription
 */
class m231201_052343_add_bridge_company_id_and_partner_id_to_kasko_by_subscription extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription', 'bridge_company_id', $this->integer());
        $this->addColumn('kasko_by_subscription', 'partner_id', $this->integer());
        $this->addColumn('neo_request', 'kasko_by_subscription_policy_id', $this->integer());

        \common\models\KaskoBySubscription::updateAll(['status' => 5], ['status' => 4]);
        \common\models\KaskoBySubscription::updateAll(['status' => 6], ['status' => 5]);
        \common\models\KaskoBySubscription::updateAll(['status' => 7], ['status' => 6]);
        \common\models\KaskoBySubscription::updateAll(['status' => 8], ['status' => 7]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231201_052343_add_bridge_company_id_and_partner_id_to_kasko_by_subscription cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231201_052343_add_bridge_company_id_and_partner_id_to_kasko_by_subscription cannot be reverted.\n";

        return false;
    }
    */
}
