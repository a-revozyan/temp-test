<?php

use yii\db\Migration;

/**
 * Class m231018_072436_add_only_kapital_to_osago_table
 */
class m231018_072436_add_only_kapital_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'only_kapital', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231018_072436_add_only_kapital_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231018_072436_add_only_kapital_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
