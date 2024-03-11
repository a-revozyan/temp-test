<?php

namespace frontend\models\ProfileForms;

use common\helpers\GeneralHelper;
use common\models\Osago;
use common\models\UniqueCode;
use thamtech\uuid\validators\UuidValidator;

class CreateUniqueCodeForm extends \yii\base\Model
{
    public $osago_uuid;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['osago_uuid'], UuidValidator::className()],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'],
                'filter' => function($query){
                    return $query
                        ->andWhere(['f_user_id' => \Yii::$app->user->id])
                        ->andWhere(['in', 'status', [
                            Osago::STATUS['received_policy']
                        ]]);
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);
        if ($unique_code = UniqueCode::findOne(['clonable_id' => $osago->id]))
            return $unique_code->code;

        $code = GeneralHelper::generateRandomString([UniqueCode::className(), 'code'], 10);
        $unique_code = new UniqueCode();
        $unique_code->code = $code;
        $unique_code->clonable_id = $osago->id;
        $unique_code->discount_percent = -5;
        $unique_code->save();
        
        return $code;
    }
}