<?php

use yii\db\Migration;

/**
 * Class m230425_074707_add_uuid_to_kasko_by_subscription_table
 */
class m230425_074707_add_uuid_to_kasko_by_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription', 'uuid', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230425_074707_add_uuid_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230425_074707_add_uuid_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }
    */
}
