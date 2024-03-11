<?php

use yii\db\Migration;

/**
 * Class m210313_183039_metatag2
 */
class m210313_183039_metatag2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('page', 'description_ru', $this->text());
        $this->alterColumn('page', 'description_uz', $this->text());
        $this->alterColumn('page', 'description_en', $this->text());
        $this->alterColumn('page', 'keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210313_183039_metatag2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210313_183039_metatag2 cannot be reverted.\n";

        return false;
    }
    */
}
