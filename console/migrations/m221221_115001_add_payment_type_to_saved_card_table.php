<?php

use yii\db\Migration;

/**
 * Class m221221_115001_add_payment_type_to_saved_card_table
 */
class m221221_115001_add_payment_type_to_saved_card_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('saved_card', 'payment_type', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221221_115001_add_payment_type_to_saved_card_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221221_115001_add_payment_type_to_saved_card_table cannot be reverted.\n";

        return false;
    }
    */
}
