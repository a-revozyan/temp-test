<?php

namespace console\controllers;

use backapi\models\forms\smsTemplateForms\RunForm;
use common\models\SmsTemplate;
use yii\base\Controller;
use yii\helpers\VarDumper;

class SmsTemplateController extends Controller
{
    public function actionRun()
    {
        $sms_templates = SmsTemplate::find()->where([
            'and',
            ['<', 'begin_date', date('Y-m-d H:i:s')],
            ['status' => SmsTemplate::STATUS['created']],
        ])->all();
        foreach ($sms_templates as $sms_template) {
            $model = new RunForm();
            $model->sms_template_id = $sms_template->id;
            $model->save();
        }
    }
}