<?php

use yii\db\Migration;

/**
 * Class m201008_115924_travel_trans
 */
class m201008_115924_travel_trans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'trans_id', $this->integer());
        $this->addColumn('osago', 'trans_id', $this->integer());

        $this->createIndex(
            'idx-travel-trans_id',
            'travel',
            'trans_id'
        );

        $this->addForeignKey(
            'fk-travel-trans_id',
            'travel',
            'trans_id',
            'transaction',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-osago-trans_id',
            'osago',
            'trans_id'
        );

        $this->addForeignKey(
            'fk-osago-trans_id',
            'osago',
            'trans_id',
            'transaction',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201008_115924_travel_trans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201008_115924_travel_trans cannot be reverted.\n";

        return false;
    }
    */
}
