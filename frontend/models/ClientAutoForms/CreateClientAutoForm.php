<?php
namespace frontend\models\ClientAutoForms;

use common\models\Autocomp;

use common\models\ClientAuto;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

class CreateClientAutoForm extends Model
{
    public $autocomp_id;
    public $manufacture_year;
    public $autonumber;
    public $tex_pass_series;
    public $tex_pass_number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autonumber', 'tex_pass_series', 'tex_pass_number'], 'required'],
            [['autocomp_id', 'manufacture_year'], 'integer'],
            [['autonumber', 'tex_pass_series', 'tex_pass_number'], 'string'],
            [['autonumber', 'tex_pass_series', 'tex_pass_number'], 'filter', 'filter' => 'trim'],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id']],
        ];
    }

    public function save()
    {
        $client_auto = new ClientAuto();
        $client_auto->setAttributes($this->attributes);
        $client_auto->f_user_id = Yii::$app->user->identity->getId();
        $client_auto->created_at = date('Y-m-d H:i:s');
        $client_auto->save();

        return $client_auto;
    }

}