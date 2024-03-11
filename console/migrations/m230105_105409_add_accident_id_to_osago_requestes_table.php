<?php

use yii\db\Migration;

/**
 * Class m230105_105409_add_accident_id_to_osago_requestes_table
 */
class m230105_105409_add_accident_id_to_osago_requestes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_requestes', 'accident_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230105_105409_add_accident_id_to_osago_requestes_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230105_105409_add_accident_id_to_osago_requestes_table cannot be reverted.\n";

        return false;
    }
    */
}
