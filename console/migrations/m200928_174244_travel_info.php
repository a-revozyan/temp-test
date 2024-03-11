<?php

use yii\db\Migration;

/**
 * Class m200928_174244_travel_info
 */
class m200928_174244_travel_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%travel_partner_info}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'assistance' => $this->string()->notNull(),
            'franchise' => $this->string()->notNull(),
            'limitation' => $this->string()->notNull(),
            'rules' => $this->string()->notNull(),
            'policy_example' => $this->string()->notNull(),
        ], null);

        $this->createIndex(
            'idx-travel_partner_info-partner_id',
            'travel_partner_info',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-travel_partner_info-partner_id',
            'travel_partner_info',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200928_174244_travel_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200928_174244_travel_info cannot be reverted.\n";

        return false;
    }
    */
}
