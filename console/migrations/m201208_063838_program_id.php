<?php

use yii\db\Migration;

/**
 * Class m201208_063838_program_id
 */
class m201208_063838_program_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('accident', 'program_id', $this->integer());

        $this->createIndex(
            'idx-accident-program_id',
            'accident',
            'program_id'
        );

        $this->addForeignKey(
            'fk-accident-program_id',
            'accident',
            'program_id',
            'accident_partner_program',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_063838_program_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_063838_program_id cannot be reverted.\n";

        return false;
    }
    */
}
