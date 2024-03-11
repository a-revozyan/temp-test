<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "automodel".
 *
 * @property int $id
 * @property int $autobrand_id
 * @property int $auto_risk_type_id
 * @property int $status
 * @property string $name
 *
 * @property Autocomp[] $autocomps
 * @property Autobrand $autobrand
 */
class Automodel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'automodel';
    }

    public const status = [
        'active' => 1,
        'inactive' => 0,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auto_risk_type_id'], 'safe'],
            [['autobrand_id', 'name', 'status'], 'required'],
            [['autobrand_id'], 'default', 'value' => null],
            [['autobrand_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['autobrand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autobrand::className(), 'targetAttribute' => ['autobrand_id' => 'id']],
            [['order'], 'double'],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'autobrand';
        $fields[] = 'autoRiskType';

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'autobrand_id' => Yii::t('app', 'Autobrand ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * Gets query for [[Autocomps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutocomps()
    {
        return $this->hasMany(Autocomp::className(), ['automodel_id' => 'id']);
    }

    /**
     * Gets query for [[Autobrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutobrand()
    {
        return $this->hasOne(Autobrand::className(), ['id' => 'autobrand_id']);
    }

    public function getAutoRiskType()
    {
        return $this->hasOne(AutoRiskType::className(), ['id' => 'auto_risk_type_id']);
    }

    public static function getFullWithParentCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullWithParent();
        }

        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    public function getWithParent()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'autobrand' => !is_null($this->autobrand) ? $this->autobrand->getShortArr() : null,
        ];
    }

    public function getFullWithParent()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->order,
            'status' => $this->status,
            'autobrand' => !is_null($this->autobrand) ? $this->autobrand->getShortArr() : null,
            'autoRiskType' => !is_null($this->autoRiskType) ? $this->autoRiskType->getShortArr() : null,
        ];
    }
}
