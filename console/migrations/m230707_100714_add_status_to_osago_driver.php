<?php

use yii\db\Migration;

/**
 * Class m230707_100714_add_status_to_osago_driver
 */
class m230707_100714_add_status_to_osago_driver extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_driver', 'status', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230707_100714_add_status_to_osago_driver cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230707_100714_add_status_to_osago_driver cannot be reverted.\n";

        return false;
    }
    */
}
