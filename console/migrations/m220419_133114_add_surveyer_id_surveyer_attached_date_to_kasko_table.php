<?php

use yii\db\Migration;

/**
 * Class m220419_133114_add_surveyer_id_surveyer_attached_date_to_kasko_table
 */
class m220419_133114_add_surveyer_id_surveyer_attached_date_to_kasko_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%kasko}}', 'surveyer_id', $this->integer());
        $this->addColumn('{{%kasko}}', 'payed_date', $this->integer());
        $this->addColumn('{{%kasko}}', 'surveyer_comment', $this->text());
        $this->addColumn('{{%kasko}}', 'processed_date', $this->integer());

        $this->addForeignKey(
            'fk-kasko-surveyer_id',
            'kasko',
            'surveyer_id',
            'user',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220419_133114_add_surveyer_id_surveyer_attached_date_to_kasko_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220419_133114_add_surveyer_id_surveyer_attached_date_to_kasko_table cannot be reverted.\n";

        return false;
    }
    */
}
