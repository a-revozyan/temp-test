<?php
namespace backapi\models\forms\kaskoTariffForms;

use common\models\AutoRiskType;
use common\models\KaskoTariff;
use yii\base\Model;


class AttachAutoRiskTypesToKaskoTariffForm extends Model
{
    public $tariff_id;
    public $auto_risk_type_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id'], 'required'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id'], 'filter' => function($query){
                return $query->andWhere(['is_islomic' => 0]);
            }],
            [['auto_risk_type_ids'], 'each',  'rule' => ['exist', 'skipOnError' => true, 'targetClass' => AutoRiskType::className(), 'targetAttribute' => ['auto_risk_type_ids' => 'id']]]
        ];
    }

    public function save()
    {
        $kasko_tariff = KaskoTariff::findOne($this->tariff_id);
        $kasko_tariff->unlinkAll('autoRiskTypes', true);
        foreach ($this->auto_risk_type_ids as $auto_risk_type_id) {
            $auto_risk_type = AutoRiskType::findOne($auto_risk_type_id);
            $kasko_tariff->link('autoRiskTypes', $auto_risk_type);
        }

        return $kasko_tariff;
    }

}