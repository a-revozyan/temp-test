<?php

use yii\db\Migration;

/**
 * Class m230414_061941_add_verified_at_sent_sms_promo_at_to_tokent_table
 */
class m230414_061941_add_verified_at_sent_sms_promo_at_to_tokent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('token', 'verified_at', $this->timestamp());
        $this->addColumn('token', 'sent_sms_promo_at', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230414_061941_add_verified_at_sent_sms_promo_at_to_tokent_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230414_061941_add_verified_at_sent_sms_promo_at_to_tokent_table cannot be reverted.\n";

        return false;
    }
    */
}
