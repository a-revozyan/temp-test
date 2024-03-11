<?php

use yii\db\Migration;

/**
 * Class m221107_153631_add_insurer_license_seria_and_number_to_osago
 */
class m221107_153631_add_insurer_license_seria_and_number_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'insurer_license_series', $this->string());
        $this->addColumn('osago', 'insurer_license_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221107_153631_add_insurer_license_seria_and_number_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221107_153631_add_insurer_license_seria_and_number_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
