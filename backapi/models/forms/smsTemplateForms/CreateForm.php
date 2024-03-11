<?php
namespace backapi\models\forms\smsTemplateForms;

use common\models\NumberDrivers;
use common\models\Product;
use common\models\SmsTemplate;
use common\models\User;
use common\services\TelegramService;
use Yii;
use yii\base\Model;


class CreateForm extends Model
{
    public $text;
    public $method;
    public $file_url;
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
            [['text', 'region_car_numbers'], 'safe'],
            [['type', 'method'], 'required'],
            [['method'], 'filter', 'filter' => 'trim'],
            [['type'], 'in', 'range' => SmsTemplate::TYPE],
            [['method'], 'in', 'range' => TelegramService::METHOD],
            [['text'], 'required', 'when' => function($model) {
                return $this->method == TelegramService::METHOD['sendMessage'];
            }],
            [['file_url'], 'required', 'when' => function($model) {
                return in_array($this->method, [
                    TelegramService::METHOD['sendVideo'],
                    TelegramService::METHOD['sendPhoto'],
                    TelegramService::METHOD['sendDocument'],
                ]);
            }],
            [['file_url'], 'file', 'extensions'=>'jpg, gif, png, jpeg, webp', 'maxSize' => 1024 * 1024 * 5, 'when' => function($model) {
                return $this->method == TelegramService::METHOD['sendPhoto'];
            }],
            [['file_url'], 'file', 'extensions'=>'mp4', 'maxSize' => 1024 * 1024 * 20, 'when' => function($model) {
                return $this->method == TelegramService::METHOD['sendVideo'];
            }],
            [['file_url'], 'file', 'extensions'=>'pdf, gif, zip', 'maxSize' => 1024 * 1024 * 20, 'when' => function($model) {
                return $this->method == TelegramService::METHOD['sendDocument'];
            }],
            [['region_car_numbers'], 'each', 'rule' => ['string', 'skipOnEmpty' => true], 'skipOnEmpty' => true],
            [['number_drivers_id', 'product'], 'integer'],
            [['product'], 'in', 'range' => Product::products],
            [['number_drivers_id'], 'exist', 'skipOnError' => true, 'targetClass' => NumberDrivers::className(), 'targetAttribute' => ['number_drivers_id' => 'id']],
            [['registered_from_date', 'registered_till_date', 'bought_from_date', 'bought_till_date', 'begin_date'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function save()
    {
        $this->region_car_numbers = is_array($this->region_car_numbers) ? array_filter($this->region_car_numbers) : $this->region_car_numbers;

        $sms_template = new SmsTemplate();
        $sms_template->setAttributes($this->attributes);
        $sms_template->created_at = date('Y-m-d H:i:s');
        $sms_template->region_car_numbers = implode(',', $this->region_car_numbers ?? []);
        if (!empty($this->file_url))
        {
            $sms_template->file_url = 'sms_template_' . date('ymdHis') . '.' . $this->file_url->extension;
            $this->file_url->saveAs(Yii::getAlias('@backapi') . "/web/uploads/sms_templates/" . $sms_template->file_url);
        }

        $users = User::getSmsFilteredUsersQuery($this->attributes);
        $sms_template->all_users_count = $users->count();
        $sms_template->status = SmsTemplate::STATUS['created'];
        $sms_template->save();

        return $sms_template;
    }

}