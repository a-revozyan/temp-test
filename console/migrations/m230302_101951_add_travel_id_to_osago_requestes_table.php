<?php

use yii\db\Migration;

/**
 * Class m230302_101951_add_travel_id_to_osago_requestes_table
 */
class m230302_101951_add_travel_id_to_osago_requestes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_requestes', 'travel_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230302_101951_add_travel_id_to_osago_requestes_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230302_101951_add_travel_id_to_osago_requestes_table cannot be reverted.\n";

        return false;
    }
    */
}
