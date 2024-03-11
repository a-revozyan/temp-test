<?php

use yii\db\Migration;

/**
 * Class m230418_111459_add_amount_uzs_to_kbsp
 */
class m230418_111459_add_amount_uzs_to_kbsp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription_policy', 'amount_uzs', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230418_111459_add_amount_uzs_to_kbsp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230418_111459_add_amount_uzs_to_kbsp cannot be reverted.\n";

        return false;
    }
    */
}
