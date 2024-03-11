<?php

use yii\db\Migration;

/**
 * Class m201019_081112_osago_rating
 */
class m201019_081112_osago_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('osago', 'passport_file', $this->string());
        $this->addColumn('osago', 'tech_passport_file', $this->string());

        $this->createTable('{{%osago_partner_rating}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'rating' => $this->string()->notNull(),
            'order_no' => $this->integer()->notNull(),
        ], null);

        $this->createIndex(
            'idx-osago_partner_rating-partner_id',
            'osago_partner_rating',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-osago_partner_rating-partner_id',
            'osago_partner_rating',
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
        echo "m201019_081112_osago_rating cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_081112_osago_rating cannot be reverted.\n";

        return false;
    }
    */
}
