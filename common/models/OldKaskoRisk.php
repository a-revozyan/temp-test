<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "old_kasko_risk".
 *
 * @property int $id
 * @property int|null $kasko_risk_id
 * @property string|null $name_ru
 * @property string|null $name_uz
 * @property string|null $name_en
 * @property int|null $category_id
 * @property float|null $amount
 * @property string|null $description_ru
 * @property string|null $description_en
 * @property string|null $description_uz
 * @property int|null $show_desc
 * @property int|null $tariff_id
 * @property int|null $tariff_partner_id
 * @property string|null $tariff_name
 * @property string|null $tariff_amount_kind
 * @property float|null $tariff_amount
 * @property string|null $tariff_franchise_ru
 * @property string|null $tariff_franchise_uz
 * @property string|null $tariff_franchise_en
 * @property string|null $tariff_only_first_risk_ru
 * @property string|null $tariff_only_first_risk_uz
 * @property string|null $tariff_only_first_risk_en
 * @property int|null $tariff_is_conditional
 */
class OldKaskoRisk extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'old_kasko_risk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kasko_risk_id', 'category_id', 'show_desc', 'tariff_id', 'tariff_partner_id', 'tariff_is_conditional'], 'default', 'value' => null],
            [['kasko_risk_id', 'category_id', 'show_desc', 'tariff_id', 'tariff_partner_id', 'tariff_is_conditional'], 'integer'],
            [['amount', 'tariff_amount'], 'number'],
            [['name_ru', 'name_uz', 'name_en', 'description_ru', 'description_en', 'description_uz', 'tariff_name', 'tariff_amount_kind'], 'string', 'max' => 255],
            [['tariff_franchise_ru', 'tariff_franchise_uz', 'tariff_franchise_en', 'tariff_only_first_risk_ru', 'tariff_only_first_risk_uz', 'tariff_only_first_risk_en'], 'string', 'max' => 10485760],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kasko_risk_id' => 'Kasko Risk ID',
            'name_ru' => 'Name Ru',
            'name_uz' => 'Name Uz',
            'name_en' => 'Name En',
            'category_id' => 'Category ID',
            'amount' => 'Amount',
            'description_ru' => 'Description Ru',
            'description_en' => 'Description En',
            'description_uz' => 'Description Uz',
            'show_desc' => 'Show Desc',
            'tariff_id' => 'Tariff ID',
            'tariff_partner_id' => 'Tariff Partner ID',
            'tariff_name' => 'Tariff Name',
            'tariff_amount_kind' => 'Tariff Amount Kind',
            'tariff_amount' => 'Tariff Amount',
            'tariff_franchise_ru' => 'Tariff Franchise Ru',
            'tariff_franchise_uz' => 'Tariff Franchise Uz',
            'tariff_franchise_en' => 'Tariff Franchise En',
            'tariff_only_first_risk_ru' => 'Tariff Only First Risk Ru',
            'tariff_only_first_risk_uz' => 'Tariff Only First Risk Uz',
            'tariff_only_first_risk_en' => 'Tariff Only First Risk En',
            'tariff_is_conditional' => 'Tariff Is Conditional',
        ];
    }

    public static function getShortArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getShortArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . $lang}
        ];
    }
}
