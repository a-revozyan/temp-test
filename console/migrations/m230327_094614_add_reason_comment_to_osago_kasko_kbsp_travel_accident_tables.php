<?php

use yii\db\Migration;

/**
 * Class m230327_094614_add_reason_comment_to_osago_kasko_kbsp_travel_accident_tables
 */
class m230327_094614_add_reason_comment_to_osago_kasko_kbsp_travel_accident_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'reason_id', $this->integer());
        $this->addColumn('osago', 'comment', $this->string(10485760));

        $this->addColumn('kasko', 'reason_id', $this->integer());
        $this->addColumn('kasko', 'comment', $this->string(10485760));

        $this->addColumn('kasko_by_subscription_policy', 'reason_id', $this->integer());
        $this->addColumn('kasko_by_subscription_policy', 'comment', $this->string(10485760));

        $this->addColumn('accident', 'reason_id', $this->integer());
        $this->addColumn('accident', 'comment', $this->string(10485760));

        $this->addColumn('travel', 'reason_id', $this->integer());
        $this->addColumn('travel', 'comment', $this->string(10485760));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230327_094614_add_reason_comment_to_osago_kasko_kbsp_travel_accident_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230327_094614_add_reason_comment_to_osago_kasko_kbsp_travel_accident_tables cannot be reverted.\n";

        return false;
    }
    */
}
