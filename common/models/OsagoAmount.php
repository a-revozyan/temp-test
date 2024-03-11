<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "osago_amount".
 *
 * @property int $id
 * @property float $insurance_premium
 * @property float $insurance_amount
 */
class OsagoAmount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'osago_amount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['insurance_premium', 'insurance_amount'], 'required'],
            [['insurance_premium', 'insurance_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'insurance_premium' => Yii::t('app', 'Insurance Premium'),
            'insurance_amount' => Yii::t('app', 'Insurance Amount'),
        ];
    }
}
