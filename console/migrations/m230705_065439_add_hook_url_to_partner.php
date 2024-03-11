<?php

use yii\db\Migration;

/**
 * Class m230705_065439_add_hook_url_to_partner
 */
class m230705_065439_add_hook_url_to_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('partner', 'hook_url', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230705_065439_add_hook_url_to_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230705_065439_add_hook_url_to_partner cannot be reverted.\n";

        return false;
    }
    */
}
