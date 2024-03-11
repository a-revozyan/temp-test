<?php
namespace saas\models\CarInspectionForms;

use common\models\CarInspection;
use common\models\Client;
use common\models\PartnerAutoBrand;
use common\models\PartnerAutoModel;
use common\services\SMSService;
use thamtech\uuid\helpers\UuidHelper;
use yii\base\Model;

class CreateCarInspectionForm extends Model
{
    public $autonumber;
    public $phone;
    public $tex_pass_series;
    public $tex_pass_number;
    public $vin;
    public $auto_model;
    public $name;
    public $year;

    public $partner_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autonumber', 'phone', 'tex_pass_series', 'tex_pass_number', 'vin', 'auto_model', 'name', 'year'], 'required'],
            [['year'], 'integer'],
            [['autonumber', 'phone', 'vin', 'auto_model', 'name'], 'string', 'max' => 255],
            [['phone', 'auto_model', 'name'], 'filter', 'filter' => 'trim'],
            [['phone'], 'match', 'pattern' => '/[9]{2}[8][0-9]{2}[0-9]{3}[0-9]{2}[0-9]{2}$/', 'message'=>'phone format is not correct']
        ];
    }

    public function save()
    {
        $this->partner_id = \Yii::$app->getUser()->identity->partner->id;
        return CarInspection::create($this);
    }

}