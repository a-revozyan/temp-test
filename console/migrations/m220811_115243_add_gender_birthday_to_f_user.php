<?php

use yii\db\Migration;

/**
 * Class m220811_115243_add_gender_birthday_to_f_user
 */
class m220811_115243_add_gender_birthday_to_f_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'gender', $this->integer());
        $this->addColumn('f_user', 'birthday', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220811_115243_add_gender_birthday_to_f_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220811_115243_add_gender_birthday_to_f_user cannot be reverted.\n";

        return false;
    }
    */
}
