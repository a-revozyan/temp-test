<?php

use yii\db\Migration;

/**
 * Class m220608_171714_travel_member_table
 */
class m220608_171714_travel_member_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%travel_member}}', [
            'id' => $this->primaryKey(),
            'travel_id' => $this->integer(),
            'age' => $this->integer(),
            'position' => $this->integer(), // oddiy sherikmi, ota-onami, bolami
        ], null);

        $this->addForeignKey(
            'fk-travel_member-travel_id',
            'travel_member',
            'travel_id',
            'travel',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_171714_travel_member_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220608_171714_travel_member_table cannot be reverted.\n";

        return false;
    }
    */
}
