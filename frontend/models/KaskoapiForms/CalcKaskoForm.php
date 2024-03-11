<?php

namespace frontend\models\KaskoapiForms;

use common\helpers\GeneralHelper;
use common\models\Autocomp;
use common\models\CarAccessory;
use common\models\Kasko;
use Yii;

class CalcKaskoForm extends \yii\base\Model
{
    public $autocomp_id;
    public $year;
    public $selected_price;
    public $is_islomic;
    public $car_accessory_ids;
    public $car_accessory_amounts;

    public function rules()
    {
        $calcAutoCompPriceForm = new CalcAutoCompPriceForm();
        $calcAutoCompPriceForm->setAttributes(\Yii::$app->request->get());
        $autoCompPrice = 0;
        if ($calcAutoCompPriceForm->validate())
            $autoCompPrice = $calcAutoCompPriceForm->calc();

        return [
            [['autocomp_id', 'year', 'selected_price', 'is_islomic'], 'required'],
            [['autocomp_id', 'year', 'selected_price', 'is_islomic'], 'integer'],
            [['is_islomic'], 'in', 'range' => [0,1]],
            [['year'], 'in', 'range' => range(GeneralHelper::env('begin_year_of_kasko'), date('Y'))],
            [['selected_price'], 'integer', 'min' => 30000000, 'max' => $autoCompPrice, 'when' => function($model) use($autoCompPrice){
                return $autoCompPrice;
            }],
            [['car_accessory_ids', 'car_accessory_amounts'], 'default', 'value' => []],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id'], 'filter' => function($query){
                return $query->andWhere(['status' => Autocomp::status['active']]);
            }],
            [['car_accessory_ids'], 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => CarAccessory::className(), 'targetAttribute' => ['car_accessory_ids' => 'id']]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'autocomp_id' => Yii::t('app', 'autocomp_id'),
            'year' => Yii::t('app', 'year'),
            'selected_price' => Yii::t('app', 'selected_price'),
            'is_islomic' => Yii::t('app', 'is_islomic'),
            'car_accessory_ids' => Yii::t('app', 'car_accessory_ids'),
            'car_accessory_amounts' => Yii::t('app', 'car_accessory_amounts'),
        ];
    }

    public function calc()
    {
        $model = new Kasko();
        $model->autocomp_id = $this->autocomp_id;
        $model->year = $this->year;
        $model->price_coeff = 1;

        $autocomp = Autocomp::findOne($this->autocomp_id);

        $tariffs = $model->calc2(
            $this->selected_price,
            $this->is_islomic,
            $autocomp->automodel->auto_risk_type_id,
            $this->car_accessory_ids,
            $this->car_accessory_amounts,
        );

        $amount = array();
        foreach ($tariffs as $key => $row)
        {
            $amount[$key] = $row['amount'];
        }
        array_multisort($amount, SORT_ASC, $tariffs);

        return $tariffs;
    }
}