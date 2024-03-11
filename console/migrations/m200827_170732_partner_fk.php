<?php

use yii\db\Migration;

/**
 * Class m200827_170732_partner_fk
 */
class m200827_170732_partner_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-partner_product-partner_id',
            'partner_product',
            'partner_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-partner_product-partner_id',
            'partner_product',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-partner_product-product_id',
            'partner_product',
            'product_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-partner_product-product_id',
            'partner_product',
            'product_id',
            'product',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200827_170732_partner_fk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200827_170732_partner_fk cannot be reverted.\n";

        return false;
    }
    */
}
