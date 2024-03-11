<?php

use yii\db\Migration;

/**
 * Class m230309_115932_add_program_name_to_travel
 */
class m230309_115932_add_program_name_to_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'program_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230309_115932_add_program_name_to_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230309_115932_add_program_name_to_travel cannot be reverted.\n";

        return false;
    }
    */
}
