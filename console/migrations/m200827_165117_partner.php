<?php

use yii\db\Migration;

/**
 * Class m200827_165117_partner
 */
class m200827_165117_partner extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%partner}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'image' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], null);

        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'code' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], null);

        $this->createTable('{{%partner_product}}', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
        ], null);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200827_165117_partner cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200827_165117_partner cannot be reverted.\n";

        return false;
    }
    */
}
