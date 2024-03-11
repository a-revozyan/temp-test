<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tariff_car_accessory_coeff".
 *
 * @property int $id
 * @property int|null $tariff_id
 * @property int|null $car_accessory_id
 * @property float|null $coeff
 */
class TariffCarAccessoryCoeff extends \yii\db\ActiveRecord
{
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'carAccessory';

        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff_car_accessory_coeff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id', 'car_accessory_id'], 'default', 'value' => null],
            [['tariff_id', 'car_accessory_id'], 'integer'],
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
            'tariff_id' => Yii::t('app', 'Tariff ID'),
            'car_accessory_id' => Yii::t('app', 'Car Accessory ID'),
            'coeff' => Yii::t('app', 'Coeff'),
        ];
    }

    public function getCarAccessory()
    {
        return $this->hasOne(CarAccessory::className(), ['id' => 'car_accessory_id']);
    }

    public static function getMergeCarAccessoryArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getMergeCarAccessoryArr();
        }

        return $_models;
    }
    public function getMergeCarAccessoryArr()
    {
        return array_merge(
            $this->carAccessory->getForIdNameArr(),
            ['coeff' => $this->coeff]
        );
    }
}
