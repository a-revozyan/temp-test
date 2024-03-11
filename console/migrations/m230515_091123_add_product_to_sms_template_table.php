<?php

use yii\db\Migration;

/**
 * Class m230515_091123_add_product_to_sms_template_table
 */
class m230515_091123_add_product_to_sms_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('sms_template', 'product', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230515_091123_add_product_to_sms_template_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230515_091123_add_product_to_sms_template_table cannot be reverted.\n";

        return false;
    }
    */
}
