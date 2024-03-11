<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "kasko_risk".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property string $description_ru
 * @property string $description_uz
 * @property string $description_en
 * @property int $category_id
 * @property float $amount
 * @property integer $show_desc
 *
 * @property KaskoTariffRisk[] $kaskoTariffRisks
 * @property KaskoRiskCategory $category
 */
class KaskoRisk extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_risk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_uz', 'name_en'], 'required'],
            [['name_ru', 'name_uz', 'name_en', "description_ru", "description_en", "description_uz"], 'string', 'max' => 255],
            [['amount'], 'number'],
            [['category_id', 'show_desc'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'name_en' => Yii::t('app', 'Name En'),
            'category' => Yii::t('app', 'Category'),
            'amount' => Yii::t('app', 'Amount'),
            'description_ru' => Yii::t('app', 'Dscription Ru'),
            'description_en' => Yii::t('app', 'Dscription En'),
            'description_uz' => Yii::t('app', 'Dscription Uz'),
            'show_desc' => Yii::t('app', 'Show description'),
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'category';

        return $fields;
    }

    /**
     * Gets query for [[KaskoTariffRisks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskoTariffRisks()
    {
        return $this->hasMany(KaskoTariffRisk::className(), ['risk_id' => 'id']);
    }

    /**
     * Gets query for [[KaskoTariffs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskoTariffs()
    {
        return $this->hasMany(KaskoTariff::className(), ['id' => 'tariff_id'])->via('kaskoTariffRisks');
    }

    /**
     * Gets query for [[KaskoRiskCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(KaskoRiskCategory::className(), ['id' => 'category_id']);
    }

    public static function getShortArrCollection($kasko_risks)
    {
        $_kasko_risks = [];
        foreach ($kasko_risks as $kasko_risk) {
            $_kasko_risks[] = $kasko_risk->getShortArr();
        }

        return $_kasko_risks;
    }

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public static function getForIdNameCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getForIdNameArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . $lang},
            'description' => $this->{'description_' . $lang},
            'show_desc' => $this->show_desc,
            'category' => !is_null($this->category) ? $this->category->getShortArr() : null,
        ];
    }

    public function getForIdNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ru,
        ];
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ru' => $this->name_ru,
            'name_uz' => $this->name_uz,
            'description_en' => $this->description_en,
            'description_ru' => $this->description_ru,
            'description_uz' => $this->description_uz,
            'amount' => $this->amount,
            'show_desc' => $this->show_desc,
            'category' => !is_null($this->category) ? $this->category->getShortArr() : null,
        ];
    }
}
