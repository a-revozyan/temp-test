<?php

namespace frontend\models\CascoStepForms;

use common\helpers\GeneralHelper;
use common\helpers\PdfHelper;
use common\models\Kasko;
use frontend\controllers\ProductController;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\web\BadRequestHttpException;

class DonwloadPolicyForm extends \yii\base\Model
{
    public $kasko_uuid;

    public function rules()
    {
        return [
            ['kasko_uuid', 'required'],
            ['kasko_uuid', UuidValidator::className()],
            [['kasko_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_uuid' => 'uuid'],
                    'filter' => function($query){
                        return $query->andWhere(['IN', 'status', [
                            Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']
                        ]]);
                    }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kasko_uuid' => Yii::t('app', 'kasko'),
        ];
    }

    public function download()
    {
        $casco = Kasko::findOne(['uuid' => $this->kasko_uuid]);
        $warehouse = $casco->warehouse;
        if (empty($warehouse->series) or empty($warehouse->number))
            throw new BadRequestHttpException(Yii::t('app', "Kechirasiz polisingiz raqami hali yozilmagan", [], GeneralHelper::lang_of_local()));
        $casco->status = Kasko::STATUS['policy_generated'];
        $casco->save();

        $pdf = PdfHelper::genKaskoPolicyPdf($casco->id);
        return base64_encode($pdf->render());
    }
}