<?php

use yii\db\Migration;

/**
 * Class m230106_125740_add_owner_with_accident_to_osago_table
 */
class m230106_125740_add_owner_with_accident_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'owner_with_accident', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230106_125740_add_owner_with_accident_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230106_125740_add_owner_with_accident_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
