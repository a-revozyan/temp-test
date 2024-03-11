<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reason".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 */
class Reason extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reason';
    }

    public const STATUS = [
        'active' => 1,
        'inactive' => 0,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 10485760],
            [['status'], 'integer'],
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

    public function getOsagos()
    {
        return $this->hasMany(Osago::className(), ['reason_id' => 'id']);
    }

    public function getKaskos()
    {
        return $this->hasMany(Kasko::className(), ['reason_id' => 'id']);
    }

    public function getTravels()
    {
        return $this->hasMany(Travel::className(), ['reason_id' => 'id']);
    }

    public function getAccidents()
    {
        return $this->hasMany(Accident::className(), ['reason_id' => 'id']);
    }

    public function getKaskoBySubscriptionPolicies()
    {
        return $this->hasMany(KaskoBySubscriptionPolicy::className(), ['reason_id' => 'id']);
    }

    public static function getShortArrCollection($reasons)
    {
        $_reason = [];
        foreach ($reasons as $reason) {
            $_reason[] = $reason->getShortArr();
        }

        return $_reason;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
        ];
    }
}
