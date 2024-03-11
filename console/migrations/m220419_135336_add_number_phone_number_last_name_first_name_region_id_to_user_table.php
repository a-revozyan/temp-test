<?php

use yii\db\Migration;

/**
 * Class m220419_135336_add_number_phone_number_last_name_first_name_region_id_to_user_table
 */
class m220419_135336_add_number_phone_number_last_name_first_name_region_id_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%user}}", 'number', $this->string()->unique());
        $this->addColumn("{{%user}}", 'phone_number', $this->string());
        $this->addColumn("{{%user}}", 'last_name', $this->string());
        $this->addColumn("{{%user}}", 'first_name', $this->string());
        $this->addColumn("{{%user}}", 'region_id', $this->integer());

        $this->addForeignKey(
            'fk-user-region_id',
            'user',
            'region_id',
            'region',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220419_135336_add_number_phone_number_last_name_first_name_region_id_to_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220419_135336_add_number_phone_number_last_name_first_name_region_id_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
