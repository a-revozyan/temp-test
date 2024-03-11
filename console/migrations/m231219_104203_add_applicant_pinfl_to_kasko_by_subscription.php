<?php

use yii\db\Migration;

/**
 * Class m231219_104203_add_applicant_pinfl_to_kasko_by_subscription
 */
class m231219_104203_add_applicant_pinfl_to_kasko_by_subscription extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko_by_subscription', 'applicant_pinfl', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231219_104203_add_applicant_pinfl_to_kasko_by_subscription cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231219_104203_add_applicant_pinfl_to_kasko_by_subscription cannot be reverted.\n";

        return false;
    }
    */
}
