<?php

namespace frontend\models\TravelStepForms;

use common\models\Kasko;
use common\models\Travel;
use frontend\controllers\ProductController;
use kartik\mpdf\Pdf;
use Yii;

class DonwloadPolicyForm extends \yii\base\Model
{
    public $travel_id;

    public function rules()
    {
        return [
            ['travel_id', 'required'],
            ['travel_id', 'integer'],
            [['travel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['travel_id' => 'id'],
                'filter' => function($query){
                    return $query->andWhere([
                        'f_user_id' => Yii::$app->user->id
                    ])->andWhere(['IN', 'status', [
                        Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']
                    ]]);
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'travel_id' => Yii::t('app', 'travel'),
        ];
    }

    public function download($controller)
    {
        $travel = Travel::findOne(['id' => $this->travel_id]);

        $view_pdf = 'travel-gross-extra-policy';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'destination' => Pdf::DEST_STRING,
            'filename' => $travel->policy_number . '.pdf',
            'content' => $controller->renderPartial($view_pdf, ['id' => $this->travel_id]),
            'cssFile' => '@frontend/web/css/bootstrap.css',
        ]);

        return base64_encode($pdf->render());
    }
}