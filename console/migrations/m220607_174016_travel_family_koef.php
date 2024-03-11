<?php

use yii\db\Migration;

/**
 * Class m220607_174016_travel_family_koef
 */
class m220607_174016_travel_family_koef extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%travel_family_koef}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer(),
            'members_count' => $this->integer(), // oilada necha kishi bo'lib sayohatga chiqayotgani
            'koef' => $this->double()    // oila bilan chiqqanda har bir ishtirokchiga nechi marta oshishi
        ], null);

        $this->addForeignKey(
            'fk-travel_family_koef-partner_id',
            'travel_family_koef',
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
        echo "m220607_174016_travel_family_koef cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220607_174016_travel_family_koef cannot be reverted.\n";

        return false;
    }
    */
}
