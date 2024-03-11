<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_partner_info".
 *
 * @property int $id
 * @property int $partner_id
 * @property string $assistance
 * @property string $franchise
 * @property string $limitation
 * @property string $rules
 * @property string $policy_example
 * @property string $assistance_uz
 * @property string $franchise_uz
 * @property string $limitation_uz
 * @property string $rules_uz
 * @property string $policy_example_uz
 * @property string $assistance_en
 * @property string $franchise_en
 * @property string $limitation_en
 * @property string $rules_en
 * @property string $policy_example_en
 *
 * @property Partner $partner
 */
class TravelPartnerInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $rulesFile;
    public $policyFile;
    public $rulesFileUz;
    public $policyFileUz;
    public $rulesFileEn;
    public $policyFileEn;

    public static function tableName()
    {
        return 'travel_partner_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'assistance', 'franchise', 'limitation'], 'required'],
            [['rulesFile', 'policyFile', 'rulesFileUz', 'policyFileUz', 'rulesFileEn', 'policyFileEn'], 'file'],
            [['partner_id'], 'default', 'value' => null],
            [['partner_id'], 'integer'],
            [['franchise', 'limitation', 'franchise_uz', 'limitation_uz', 'franchise_en', 'limitation_en'], 'string'],
            [['assistance', 'rules', 'policy_example', 'assistance_uz', 'rules_uz', 'policy_example_uz', 'assistance_en', 'rules_en', 'policy_example_en'], 'string', 'max' => 255],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
        ];
    }

    public function uploadRules()
    {
        if ($this->validate()) {
            $this->rulesFile->saveAs('../../frontend/web/uploads/travel_info/' . $this->rules);
            return true;
        } else {
            return false;
        }
    }

    public function uploadPolicy()
    {
        if ($this->validate()) {
            $this->policyFile->saveAs('../../frontend/web/uploads/travel_info/' . $this->policy_example);
            return true;
        } else {
            return false;
        }
    }

    public function uploadRulesUz()
    {
        if ($this->validate()) {
            $this->rulesFileUz->saveAs('../../frontend/web/uploads/travel_info/' . $this->rules_uz);
            return true;
        } else {
            return false;
        }
    }

    public function uploadPolicUz()
    {
        if ($this->validate()) {
            $this->policyFileUz->saveAs('../../frontend/web/uploads/travel_info/' . $this->policy_example_uz);
            return true;
        } else {
            return false;
        }
    }

    public function uploadRulesEn()
    {
        if ($this->validate()) {
            $this->rulesFileEn->saveAs('../../frontend/web/uploads/travel_info/' . $this->rules_en);
            return true;
        } else {
            return false;
        }
    }

    public function uploadPolicyEn()
    {
        if ($this->validate()) {
            $this->policyFileEn->saveAs('../../frontend/web/uploads/travel_info/' . $this->policy_example_en);
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'assistance' => Yii::t('app', 'Assistance'),
            'franchise' => Yii::t('app', 'Franchise'),
            'limitation' => Yii::t('app', 'Limitation'),
            'rules' => Yii::t('app', 'Rules'),
            'policy_example' => Yii::t('app', 'Policy Example'),
        ];
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }
}
