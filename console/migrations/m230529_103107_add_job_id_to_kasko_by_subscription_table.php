<?php

use yii\db\Migration;

/**
 * Class m230529_103107_add_job_id_to_kasko_by_subscription_table
 */
class m230529_103107_add_job_id_to_kasko_by_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription', 'job_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230529_103107_add_job_id_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230529_103107_add_job_id_to_kasko_by_subscription_table cannot be reverted.\n";

        return false;
    }
    */
}
