<?php

use yii\db\Migration;

/**
 * Class m220621_192905_add_payed_date_to_travel
 */
class m220621_192905_add_payed_date_to_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%travel}}', 'payed_date', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220621_192905_add_payed_date_to_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220621_192905_add_payed_date_to_travel cannot be reverted.\n";

        return false;
    }
    */
}
