<?php

use yii\db\Migration;

/**
 * Class m230414_093954_add_status_to_autobrand_automodel_autocomp_tables
 */
class m230414_093954_add_status_to_autobrand_automodel_autocomp_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('autobrand', 'status', $this->smallInteger());
        $this->addColumn('automodel', 'status', $this->smallInteger());
        $this->addColumn('autocomp', 'status', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230414_093954_add_status_to_autobrand_automodel_autocomp_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230414_093954_add_status_to_autobrand_automodel_autocomp_tables cannot be reverted.\n";

        return false;
    }
    */
}
