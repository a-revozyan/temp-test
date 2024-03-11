<?php

namespace frontend\models\OsagoStepForms;

use common\models\BridgeCompany;
use common\models\Osago;
use Yii;

class PayForm extends \yii\base\Model
{
    public $osago_uuid;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'],
                'filter' => function($query){
                    $bridge_company = BridgeCompany::findOne(['user_id' => Yii::$app->user->id]);
                    return $query
                        ->andWhere(['bridge_company_id' => $bridge_company->id])
                        ->andWhere(['in', 'status', [
                            Osago::STATUS['step3']
                        ]]);
                }
            ],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);
        $osago->saveAfterPayed();
        return $osago;
    }
}
