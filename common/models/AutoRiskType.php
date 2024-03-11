<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "auto_risk_type".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $status
 *
 * @property Automodel[] $automodels
 */
class AutoRiskType extends \yii\db\ActiveRecord
{

    public const STATUS = [
        'active' => 1,
        'inactive' => 0,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_risk_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS['active']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Automodels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutomodels()
    {
        return $this->hasMany(Automodel::className(), ['auto_risk_type_id' => 'id']);
    }

    public function getKaskoTariffs()
    {
        return $this->hasMany(KaskoTariff::className(), ["id" => "kasko_tariff_id"])->viaTable('auto_risk_kasko_tariff', ['auto_risk_type_id' => "id"]);
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
        ];
    }
}
