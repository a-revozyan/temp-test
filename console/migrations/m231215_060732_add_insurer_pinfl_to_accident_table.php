<?php

use yii\db\Migration;

/**
 * Class m231215_060732_add_insurer_pinfl_to_accident_table
 */
class m231215_060732_add_insurer_pinfl_to_accident_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('accident', 'insurer_pinfl', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231215_060732_add_insurer_pinfl_to_accident_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231215_060732_add_insurer_pinfl_to_accident_table cannot be reverted.\n";

        return false;
    }
    */
}
