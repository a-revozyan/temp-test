<?php
namespace backapi\models\forms\smsTemplateForms;

use common\models\NumberDrivers;
use common\models\Product;
use common\models\SmsTemplate;
use common\models\User;
use common\services\TelegramService;
use Yii;
use yii\base\Model;


class UpdateForm extends Model
{
    public $sms_template_id;
    public $text;
    public $region_car_numbers;
    public $number_drivers_id;
    public $registered_from_date;
    public $registered_till_date;
    public $bought_from_date;
    public $bought_till_date;
    public $type;
    public $begin_date;
    public $product;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'region_car_numbers', 'text'], 'safe'],
            [['type'], 'required'],
            [['type'], 'in', 'range' => SmsTemplate::TYPE],
            [['region_car_numbers'], 'each', 'rule' => ['string', 'skipOnEmpty' => false], 'skipOnEmpty' => false],
            [['number_drivers_id', 'sms_template_id', 'product'], 'integer'],
            [['number_drivers_id'], 'exist', 'skipOnError' => true, 'targetClass' => NumberDrivers::className(), 'targetAttribute' => ['number_drivers_id' => 'id']],
            [['sms_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplate::className(), 'targetAttribute' => ['sms_template_id' => 'id'], 'filter' => function($query){
                return $query->andWhere(['status' => SmsTemplate::STATUS['created']]);
            }],
            [['product'], 'in', 'range' => Product::products],
            [['registered_from_date', 'registered_till_date', 'bought_from_date', 'bought_till_date', 'begin_date'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function save()
    {
        $sms_template = SmsTemplate::findOne($this->sms_template_id);
        $sms_template->setAttributes($this->attributes);
        $sms_template->updated_at = date('Y-m-d H:i:s');
        $sms_template->region_car_numbers = implode(',', $this->region_car_numbers ?? []);

        $users = User::getSmsFilteredUsersQuery($this->attributes);
        $sms_template->all_users_count = $users->count();
        $sms_template->save();

        return $sms_template;
    }

}