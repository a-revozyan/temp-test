<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "kasko_risk".
 *
 * @property int $id
 * @property string $name
 *
 * @property KaskoRisk[] $kaskoRisks
 */
class KaskoRiskCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_risk_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Category Name'),
        ];
    }

    /**
     * Gets query for [[KaskoRisks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskoRisks()
    {
        return $this->hasMany(KaskoRisk::className(), ['category_id' => 'id']);
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
