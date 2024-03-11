<?php

use yii\db\Migration;

/**
 * Class m200827_174142_osago_ref
 */
class m200827_174142_osago_ref extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {        
        $tableOptions = null;

        $this->createTable('{{%autotype}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%citizenship}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%period}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%number_drivers}}', [
            'id' => $this->primaryKey(),
            'name_ru' => $this->string()->notNull(),
            'name_uz' => $this->string()->notNull(),
            'name_en' => $this->string()->notNull(),
            'coeff' => $this->integer()->notNull(),
        ], $tableOptions);   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200827_174142_osago_ref cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200827_174142_osago_ref cannot be reverted.\n";

        return false;
    }
    */
}
