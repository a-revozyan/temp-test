<?php

use yii\db\Migration;

/**
 * Class m210313_175211_metatag
 */
class m210313_175211_metatag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%page}}', [
            'id' => $this->primaryKey(),
            'url' => $this->string()->notNull(),
            'description_ru' => $this->string()->notNull(),
            'description_uz' => $this->string()->notNull(),
            'description_en' => $this->string()->notNull(),
            'keywords' => $this->string()->notNull(),
        ], null);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210313_175211_metatag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210313_175211_metatag cannot be reverted.\n";

        return false;
    }
    */
}
