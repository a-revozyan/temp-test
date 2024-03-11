<?php

use yii\db\Migration;

/**
 * Class m220808_111013_add_role_to_f_user_table
 */
class m220808_111013_add_role_to_f_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('f_user', 'role', $this->integer()->defaultValue(\common\models\User::ROLES['user']));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220808_111013_add_role_to_f_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220808_111013_add_role_to_f_user_table cannot be reverted.\n";

        return false;
    }
    */
}
