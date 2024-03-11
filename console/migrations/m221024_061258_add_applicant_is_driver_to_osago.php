<?php

use yii\db\Migration;

/**
 * Class m221024_061258_add_applicant_is_driver_to_osago
 */
class m221024_061258_add_applicant_is_driver_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'applicant_is_driver', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221024_061258_add_applicant_is_driver_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221024_061258_add_applicant_is_driver_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
