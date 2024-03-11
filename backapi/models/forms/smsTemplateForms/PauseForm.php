<?php
namespace backapi\models\forms\smsTemplateForms;

use common\models\SmsTemplate;
use yii\base\Model;


class PauseForm extends Model
{
    public $sms_template_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sms_template_id'], 'required'],
            [['sms_template_id'], 'integer'],
            [['sms_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplate::className(), 'targetAttribute' => ['sms_template_id' => 'id']],
        ];
    }

    public function save()
    {
        $sms_template = SmsTemplate::findOne($this->sms_template_id);
        $sms_template->status = SmsTemplate::STATUS['paused'];
        $sms_template->save();

        return $sms_template;
    }

}