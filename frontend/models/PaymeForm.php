<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PaymeForm extends Model
{
    public $method;
    public $number;
    public $phone;
    public $expiry;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            ['method', 'integer'],
            [['number', 'expiry', 'verifyCode', 'phone'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'number' => Yii::t('app', 'Card number'),
            'expiry' => Yii::t('app', 'Expire of card'),
        ];
    }
}
