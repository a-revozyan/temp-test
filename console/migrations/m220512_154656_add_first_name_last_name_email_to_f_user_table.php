<?php

use yii\db\Migration;

/**
 * Class m220512_154656_add_first_name_last_name_email_to_f_user_table
 */
class m220512_154656_add_first_name_last_name_email_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("f_user", "first_name", $this->string());
        $this->addColumn("f_user", "last_name", $this->string());
        $this->addColumn("f_user", "email", $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220512_154656_add_first_name_last_name_email_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220512_154656_add_first_name_last_name_email_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
