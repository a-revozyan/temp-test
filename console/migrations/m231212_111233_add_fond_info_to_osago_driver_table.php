<?php

use yii\db\Migration;

/**
 * Class m231212_111233_add_fond_info_to_osago_driver_table
 */
class m231212_111233_add_fond_info_to_osago_driver_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_driver', 'last_name', $this->string());
        $this->addColumn('osago_driver', 'first_name', $this->string());
        $this->addColumn('osago_driver', 'middle_name', $this->string());
        $this->addColumn('osago_driver', 'created_at', $this->dateTime());
        $this->alterColumn('osago_driver', 'osago_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231212_111233_add_fond_info_to_osago_driver_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231212_111233_add_fond_info_to_osago_driver_table cannot be reverted.\n";

        return false;
    }
    */
}
