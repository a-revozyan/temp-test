<?php

use yii\db\Migration;

/**
 * Class m220924_122611_add_insurer_birthday_to_osago_birhtday_to_osago_drivers
 */
class m220924_122611_add_insurer_birthday_to_osago_birhtday_to_osago_drivers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'insurer_birthday', $this->integer()->null());
        $this->addColumn('osago_driver', 'birthday', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220924_122611_add_insurer_birthday_to_osago_birhtday_to_osago_drivers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220924_122611_add_insurer_birthday_to_osago_birhtday_to_osago_drivers cannot be reverted.\n";

        return false;
    }
    */
}
