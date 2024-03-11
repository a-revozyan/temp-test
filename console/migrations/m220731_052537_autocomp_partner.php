<?php

use yii\db\Migration;

/**
 * Class m220731_052537_autocomp_partner
 */
class m220731_052537_autocomp_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('autocomp_partner', [
            'autocomp_id' => $this->integer(),
            'partner_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-autocomp_partner-autocomp_id',
            'autocomp_partner',
            'autocomp_id',
            'autocomp',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-autocomp_partner-partner_id',
            'autocomp_partner',
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
        echo "m220731_052537_autocomp_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220731_052537_autocomp_partner cannot be reverted.\n";

        return false;
    }
    */
}
