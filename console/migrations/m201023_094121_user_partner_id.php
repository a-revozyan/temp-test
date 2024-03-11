<?php

use yii\db\Migration;

/**
 * Class m201023_094121_user_partner_id
 */
class m201023_094121_user_partner_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'partner_id', $this->integer());

        $this->createIndex(
            'idx-user-partner_id',
            'user',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-user-partner_id',
            'user',
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
        echo "m201023_094121_user_partner_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_094121_user_partner_id cannot be reverted.\n";

        return false;
    }
    */
}
