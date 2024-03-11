<?php

namespace frontend\models\KaskoapiForms;

use common\models\Autocomp;
use common\models\Kasko;
use Yii;

class CalcAutoCompPriceForm extends \yii\base\Model
{
    public $autocomp_id;
    public $year;

    public function rules()
    {
        return [
            [['autocomp_id', 'year'], 'required'],
            [['autocomp_id', 'year'], 'integer'],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id'], 'filter' => function($query){
                return $query->andWhere(['status' => Autocomp::status['active']]);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'autocomp_id' => Yii::t('app', 'autocomp_id'),
            'year' => Yii::t('app', 'year'),
        ];
    }

    public function calc()
    {
        $autocomp_price = Autocomp::findOne($this->autocomp_id)->price;
        return Kasko::getAutoRealPrice($autocomp_price, $this->year);
    }
}