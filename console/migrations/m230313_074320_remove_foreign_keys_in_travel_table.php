<?php

use yii\db\Migration;

/**
 * Class m230313_074320_remove_foreign_keys_in_travel_table
 */
class m230313_074320_remove_foreign_keys_in_travel_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-travel-purpose_id', 'travel');
//        $this->dropForeignKey('fk-travel_program_country-program_id', 'travel');
//        $this->dropForeignKey('fk-travel_program_risk-program_id', 'travel');
//        $this->dropForeignKey('fk-travel_program_period-program_id', 'travel');
        $this->dropForeignKey('fk-travel-program_id', 'travel');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230313_074320_remove_foreign_keys_in_travel_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230313_074320_remove_foreign_keys_in_travel_table cannot be reverted.\n";

        return false;
    }
    */
}
