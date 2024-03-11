<?php

use yii\db\Migration;

/**
 * Class m200901_061845_osago_driver
 */
class m200901_061845_osago_driver extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%osago_driver}}', [
            'id' => $this->primaryKey(),
            'osago_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'pinfl' => $this->string(),
            'passport_series' => $this->string(),
            'passport_number' => $this->string(),
            'license_series' => $this->string()->notNull(),
            'license_number' => $this->string()->notNull(),
            'relationship_id' => $this->integer(),
        ], null);

        $this->createTable('{{%relationship}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull()
        ], null);

        $this->createIndex(
            'idx-osago_driver-relationship_id',
            'osago_driver',
            'relationship_id'
        );

        $this->addForeignKey(
            'fk-osago_driver-relationship_id',
            'osago_driver',
            'relationship_id',
            'relationship',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-osago_driver-osago_id',
            'osago_driver',
            'osago_id'
        );

        $this->addForeignKey(
            'fk-osago_driver-osago_id',
            'osago_driver',
            'osago_id',
            'osago',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200901_061845_osago_driver cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_061845_osago_driver cannot be reverted.\n";

        return false;
    }
    */
}
