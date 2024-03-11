<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agent_product_coeff".
 *
 * @property int $id
 * @property int|null $agent_id
 * @property int|null $product_id
 * @property float|null $coeff
 */
class AgentProductCoeff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agent_product_coeff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id', 'product_id'], 'default', 'value' => null],
            [['agent_id', 'product_id'], 'integer'],
            [['coeff'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'agent_id' => Yii::t('app', 'Agent ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'coeff' => Yii::t('app', 'Coeff'),
        ];
    }

    public function getShortArr()
    {
        return [
            "product_id" => $this->product_id,
            "coeff" => $this->coeff,
        ];
    }

    public static function getShortArrCollection($product_coeffs)
    {
        $_product_coeffs = [];
        foreach ($product_coeffs as $product_coeff) {
            $_product_coeffs[] = $product_coeff->getShortArr();
        }
        return $_product_coeffs;
    }
}
