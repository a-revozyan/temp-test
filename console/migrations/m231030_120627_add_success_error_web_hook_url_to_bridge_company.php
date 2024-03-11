<?php

use yii\db\Migration;

/**
 * Class m231030_120627_add_success_error_web_hook_url_to_bridge_company
 */
class m231030_120627_add_success_error_web_hook_url_to_bridge_company extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bridge_company', 'success_webhook_url', $this->string());
        $this->addColumn('bridge_company', 'error_webhook_url', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_120627_add_success_error_web_hook_url_to_bridge_company cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_120627_add_success_error_web_hook_url_to_bridge_company cannot be reverted.\n";

        return false;
    }
    */
}
