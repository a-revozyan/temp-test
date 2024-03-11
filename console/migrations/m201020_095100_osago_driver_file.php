<?php

use yii\db\Migration;

/**
 * Class m201020_095100_osago_driver_file
 */
class m201020_095100_osago_driver_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago_driver', 'license_file', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_095100_osago_driver_file cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_095100_osago_driver_file cannot be reverted.\n";

        return false;
    }
    */
}
