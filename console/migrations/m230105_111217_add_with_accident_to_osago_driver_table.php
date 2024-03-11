<?php

use yii\db\Migration;

/**
 * Class m230105_111217_add_with_accident_to_osago_driver_table
 */
class m230105_111217_add_with_accident_to_osago_driver_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_driver', 'with_accident', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230105_111217_add_with_accident_to_osago_driver_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230105_111217_add_with_accident_to_osago_driver_table cannot be reverted.\n";

        return false;
    }
    */
}
