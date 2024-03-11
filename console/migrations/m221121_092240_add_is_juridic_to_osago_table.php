<?php

use yii\db\Migration;

/**
 * Class m221121_092240_add_is_juridic_to_osago_table
 */
class m221121_092240_add_is_juridic_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'is_juridic', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221121_092240_add_is_juridic_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221121_092240_add_is_juridic_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
