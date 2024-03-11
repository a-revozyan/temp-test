<?php

use yii\db\Migration;

/**
 * Class m230427_033742_add_gross_auto_id_to_osago_table
 */
class m230427_033742_add_gross_auto_id_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago','gross_auto_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230427_033742_add_gross_auto_id_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230427_033742_add_gross_auto_id_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
