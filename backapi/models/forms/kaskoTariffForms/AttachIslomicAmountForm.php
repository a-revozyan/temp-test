<?php
namespace backapi\models\forms\kaskoTariffForms;

use common\models\Autocomp;
use common\models\AutoRiskType;
use common\models\KaskoRisk;
use common\models\KaskoRiskCategory;
use common\models\KaskoTariff;
use common\models\Partner;
use common\models\TariffIslomicAmount;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;


class AttachIslomicAmountForm extends Model
{
    public $tariff_id;
    public $tariff_islomic_amounts;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id'], 'required'],
            [['tariff_id'], 'integer'],
            [['tariff_islomic_amounts'], 'each', 'rule' => ['integer']],
            [['tariff_islomic_amounts'], 'exist_keys_in_tariff_islomic_amount'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id'], 'filter' => function($query){
                return $query->andWhere(['is_islomic' => 1]);
            }],
        ];
    }

    public function exist_keys_in_tariff_islomic_amount($attribute, $params)
    {
        $ids = array_keys($this->$attribute);
        $exist_ids_count = AutoRiskType::find()->where(['in', 'id', $ids])->count();

        if (count($ids) != $exist_ids_count)
            $this->addError($attribute, 'keys of tariff islomic amount are incorrect.');
    }

    public function save()
    {
        $kasko_tariff = KaskoTariff::findOne($this->tariff_id);

        $old_tariff_islomic_amount_ids = array_map(function ($tariff_islomic_amount){
            return $tariff_islomic_amount->id;
        }, $kasko_tariff->tariffIslomicAmounts);

        TariffIslomicAmount::deleteAll(['in', 'id', $old_tariff_islomic_amount_ids]);
        foreach ($this->tariff_islomic_amounts as $auto_type_id => $amount)
        {
            $tariff_islomic_amount = new TariffIslomicAmount();
            $tariff_islomic_amount->kasko_tariff_id = $kasko_tariff->id;
            $tariff_islomic_amount->auto_risk_type_id = $auto_type_id;
            $tariff_islomic_amount->amount = $amount;
            $tariff_islomic_amount->save();
        }

        return KaskoTariff::findOne($this->tariff_id);
    }
}