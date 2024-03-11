<?php

use yii\db\Migration;

/**
 * Class m230214_045750_add_unique_code_clonable_id_to_osago_table
 */
class m230214_045750_add_unique_code_clonable_id_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'unique_code_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230214_045750_add_unique_code_clonable_id_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230214_045750_add_unique_code_clonable_id_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
