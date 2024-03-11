<?php

use yii\db\Migration;

/**
 * Class m220409_113539_alter_columns_to_nullable_from_kasko_table
 */
class m220409_113539_alter_columns_to_nullable_from_kasko_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%kasko}}', 'autonumber', $this->string());
        $this->alterColumn('{{%kasko}}', 'amount_uzs', $this->float());
        $this->alterColumn('{{%kasko}}', 'amount_usd', $this->float());
        $this->alterColumn('{{%kasko}}', 'begin_date', $this->date());
        $this->alterColumn('{{%kasko}}', 'end_date', $this->date());
        $this->alterColumn('{{%kasko}}', 'insurer_name', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_address', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_phone', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_passport_series', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_passport_number', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_tech_pass_series', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_tech_pass_number', $this->string());
        $this->alterColumn('{{%kasko}}', 'insurer_pinfl', $this->string());
        $this->alterColumn('{{%kasko}}', 'partner_id', $this->integer());
        $this->addColumn('{{%kasko}}', 'f_user_id', $this->integer());

        $this->addForeignKey(
            'fk-kasko-f_user_id',
            'kasko',
            'f_user_id',
            'f_user',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220409_113539_alter_columns_to_nullable_from_kasko_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220409_113539_alter_columns_to_nullable_from_kasko_table cannot be reverted.\n";

        return false;
    }
    */
}
