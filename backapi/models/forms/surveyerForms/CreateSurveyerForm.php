<?php
namespace backapi\models\forms\surveyerForms;

use common\models\Autocomp;
use common\models\KaskoRisk;
use common\models\KaskoRiskCategory;
use common\models\KaskoTariff;
use common\models\Partner;
use common\models\Region;
use common\models\Surveyer;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;


class CreateSurveyerForm extends Model
{
    public $first_name;
    public $region_id;
    public $phone_number;
    public $password;
    public $repeat_password;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'first_name', 'phone_number', 'password', 'repeat_password', 'status'], 'required'],
            [['status'], 'in', 'range'=> [0,10]],
            [['region_id'], 'integer'],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            ['password', 'compare', 'compareAttribute' => 'repeat_password']
        ];
    }

    public function save()
    {
        $model = new Surveyer();
        $model->username = (string)time();
        $model->email = "default@surveyer.uz";
        $model->setAttributes($this->attributes);

        $model->generateAuthKey();
        $model->save();
        $model->setPassword($this->password);

        $model->created_at = time();
        $model->updated_at = time();
        if ($model->save())
        {
            $auth = Yii::$app->authManager;
            if (!($auth_item = $auth->getRole(Surveyer::SURVEYER_ROLE_NAME)))
            {
                $auth_item = $auth->createRole(Surveyer::SURVEYER_ROLE_NAME);
                $auth->add($auth_item);
            }
            $auth->assign($auth_item, $model->id);

            return $model;
        }
        return $model->errors;
    }

}