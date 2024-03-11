<?php

use yii\db\Migration;

/**
 * Class m201015_184047_travel_form
 */
class m201015_184047_travel_form extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('traveler', 'phone', $this->string());
        $this->alterColumn('traveler', 'address', $this->string());
        $this->alterColumn('travel', 'insurer_address', $this->string());
        $this->alterColumn('travel', 'insurer_pinfl', $this->string());
        $this->addColumn('travel', 'insurer_email', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201015_184047_travel_form cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_184047_travel_form cannot be reverted.\n";

        return false;
    }
    */
}
