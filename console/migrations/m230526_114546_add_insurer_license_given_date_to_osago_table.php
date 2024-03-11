<?php

use yii\db\Migration;

/**
 * Class m230526_114546_add_insurer_license_given_date_to_osago_table
 */
class m230526_114546_add_insurer_license_given_date_to_osago_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'insurer_license_given_date', $this->date());
        $this->addColumn('osago_driver', 'license_given_date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230526_114546_add_insurer_license_given_date_to_osago_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230526_114546_add_insurer_license_given_date_to_osago_table cannot be reverted.\n";

        return false;
    }
    */
}
