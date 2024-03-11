<?php

use yii\db\Migration;

/**
 * Class m220810_064652_add_agent_amount_surveyer_amount_to_kasko
 */
class m220810_064652_add_agent_amount_surveyer_amount_to_kasko extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'agent_amount', $this->integer());
        $this->addColumn('kasko', 'surveyer_amount', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_064652_add_agent_amount_surveyer_amount_to_kasko cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_064652_add_agent_amount_surveyer_amount_to_kasko cannot be reverted.\n";

        return false;
    }
    */
}
