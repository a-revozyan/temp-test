<?php

use yii\db\Migration;

/**
 * Class m230105_112502_add_osago_driver_id_to_accident_insurer_table
 */
class m230105_112502_add_osago_driver_id_to_accident_insurer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('accident_insurer', 'osago_driver_id', $this->integer());
        $this->alterColumn('accident_insurer', 'name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230105_112502_add_osago_driver_id_to_accident_insurer_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230105_112502_add_osago_driver_id_to_accident_insurer_table cannot be reverted.\n";

        return false;
    }
    */
}
