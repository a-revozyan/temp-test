<?php

namespace backapi\models\forms\kaskoForms;

use common\helpers\PdfHelper;
use common\models\Kasko;
use Yii;
use yii\web\BadRequestHttpException;

class DonwloadPolicyForm extends \yii\base\Model
{
    public $kasko_id;

    public function rules()
    {
        return [
            ['kasko_id', 'required'],
            ['kasko_id', 'integer'],
            [['kasko_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['kasko_id' => 'id'],
                    'filter' => function($query){
                        return $query->andWhere(['IN', 'status', [
                            Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated'], Kasko::STATUS['canceled'],
                        ]]);
                    }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kasko_id' => Yii::t('app', 'kasko'),
        ];
    }

    public function download()
    {
        $casco = Kasko::findOne(['id' => $this->kasko_id]);
        $warehouse = $casco->warehouse;
        if (empty($warehouse->series) or empty($warehouse->number))
            throw new BadRequestHttpException(Yii::t('app', "Kechirasiz, bu polisga raqami hali yozilmagan", [],"ru"));

        $pdf = PdfHelper::genKaskoPolicyPdf($casco->id);
        return base64_encode($pdf->render());
    }
}