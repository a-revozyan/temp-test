<?php

use yii\db\Migration;

/**
 * Class m201203_064958_guvohnoma
 */
class m201203_064958_guvohnoma extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->dropIndex(
        //     'idx-accident_insurer-accident_id',
        //     'accident_insurer'
        // );

        // $this->dropForeignKey(
        //     'fk-accident_insurer-accident_id',
        //     'accident_insurer'
        // );

        // $this->createIndex(
        //     'idx-accident_insurer-accident_id',
        //     'accident_insurer',
        //     'accident_id'
        // );

        // $this->addForeignKey(
        //     'fk-accident_insurer-accident_id',
        //     'accident_insurer',
        //     'accident_id',
        //     'accident',
        //     'id',
        //     'CASCADE'
        // );

        // $this->alterColumn('accident', 'address_delivery', $this->string());
        $this->addColumn('accident_insurer', 'identity_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201203_064958_guvohnoma cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201203_064958_guvohnoma cannot be reverted.\n";

        return false;
    }
    */
}
