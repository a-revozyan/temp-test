<?php

use yii\db\Migration;

/**
 * Class m201012_123515_kasko2
 */
class m201012_123515_kasko2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'partner_id', $this->integer()->notNull());

        $this->createIndex(
            'idx-kasko-partner_id',
            'kasko',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-kasko-partner_id',
            'kasko',
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
        echo "m201012_123515_kasko2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_123515_kasko2 cannot be reverted.\n";

        return false;
    }
    */
}
