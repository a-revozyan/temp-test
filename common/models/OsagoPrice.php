<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "osago_price".
 *
 * @property int $id
 * @property int|null $vehicle
 * @property int|null $use_territory
 * @property int|null $period
 * @property int|null $driver_limit
 * @property int|null $discount
 * @property int|null $amount
 * @property string|null $updated_at
 */
class OsagoPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'osago_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vehicle', 'use_territory', 'period', 'driver_limit', 'discount', 'amount'], 'default', 'value' => null],
            [['vehicle', 'use_territory', 'period', 'driver_limit', 'discount', 'amount'], 'integer'],
            [['updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vehicle' => 'Vehicle',
            'use_territory' => 'Use Territory',
            'period' => 'Period',
            'driver_limit' => 'Driver Limit',
            'discount' => 'Discount',
            'amount' => 'Amount',
            'updated_at' => 'Updated At',
        ];
    }
}
