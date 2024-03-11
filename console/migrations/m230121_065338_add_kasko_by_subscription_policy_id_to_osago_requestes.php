<?php

use yii\db\Migration;

/**
 * Class m230121_065338_add_kasko_by_subscription_policy_id_to_osago_requestes
 */
class m230121_065338_add_kasko_by_subscription_policy_id_to_osago_requestes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_requestes', 'kasko_by_subscription_policy_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230121_065338_add_kasko_by_subscription_policy_id_to_osago_requestes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230121_065338_add_kasko_by_subscription_policy_id_to_osago_requestes cannot be reverted.\n";

        return false;
    }
    */
}
