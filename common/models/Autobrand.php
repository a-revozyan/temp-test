<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;

/**
 * This is the model class for table "autobrand".
 *
 * @property int $id
 * @property string $name
 * @property integer $status
 *
 * @property Automodel[] $automodels
 */
class Autobrand extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'autobrand';
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
            [['name', 'status'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['order'], 'double'],
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
        ];
    }

    /**
     * Gets query for [[Automodels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutomodels()
    {
        return $this->hasMany(Automodel::className(), ['autobrand_id' => 'id']);
    }

    public static function getFullArrCollection($autobrands)
    {
        $_autobrand = [];
        foreach ($autobrands as $autobrand) {
            $_autobrand[] = $autobrand->getFullArr();
        }

        return $_autobrand;
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
            'order' => $this->order,
            'status' => $this->status,
        ];
    }
}
