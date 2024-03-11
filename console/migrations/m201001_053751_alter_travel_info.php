<?php

use yii\db\Migration;

/**
 * Class m201001_053751_alter_travel_info
 */
class m201001_053751_alter_travel_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('travel_partner_info', 'franchise', $this->text());
        $this->alterColumn('travel_partner_info', 'limitation', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201001_053751_alter_travel_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201001_053751_alter_travel_info cannot be reverted.\n";

        return false;
    }
    */
}
