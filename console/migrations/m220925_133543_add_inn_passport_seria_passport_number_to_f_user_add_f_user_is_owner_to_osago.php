<?php

use yii\db\Migration;

/**
 * Class m220925_133543_add_inn_passport_seria_passport_number_to_f_user_add_f_user_is_owner_to_osago
 */
class m220925_133543_add_inn_passport_seria_passport_number_to_f_user_add_f_user_is_owner_to_osago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'inn', $this->string());
        $this->addColumn('f_user', 'passport_seria', $this->string(2));
        $this->addColumn('f_user', 'passport_number', $this->string(7));

        $this->addColumn('osago', 'f_user_is_owner', $this->boolean());
        $this->addColumn('osago', 'insurer_inn', $this->string());

        $this->alterColumn('osago_driver', 'name', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220925_133543_add_inn_passport_seria_passport_number_to_f_user_add_f_user_is_owner_to_osago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220925_133543_add_inn_passport_seria_passport_number_to_f_user_add_f_user_is_owner_to_osago cannot be reverted.\n";

        return false;
    }
    */
}
