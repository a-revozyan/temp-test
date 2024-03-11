<?php

use yii\db\Migration;

/**
 * Class m221103_122302_make_nullable_licence_of_osago_driver
 */
class m221103_122302_make_nullable_licence_of_osago_driver extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('osago_driver', 'license_series', $this->string());
        $this->alterColumn('osago_driver', 'license_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221103_122302_make_nullable_licence_of_osago_driver cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221103_122302_make_nullable_licence_of_osago_driver cannot be reverted.\n";

        return false;
    }
    */
}
