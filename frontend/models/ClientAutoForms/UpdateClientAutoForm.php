<?php
namespace frontend\models\ClientAutoForms;

use common\models\Autocomp;
use common\models\ClientAuto;
use common\models\User;
use Yii;
use yii\base\Model;


class UpdateClientAutoForm extends Model
{
    public $id;
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
            [['id', 'autonumber', 'tex_pass_series', 'tex_pass_number'], 'required'],
            [['id', 'autocomp_id', 'manufacture_year'], 'integer'],
            [['autonumber', 'tex_pass_series', 'tex_pass_number'], 'string'],
            [['autonumber', 'tex_pass_series', 'tex_pass_number'], 'filter', 'filter' => 'trim'],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientAuto::className(), 'targetAttribute' => ['id' => 'id'], 'filter' => function($query){
                return $query->where(['f_user_id' => Yii::$app->user->identity->getId()]);
            }],
        ];
    }

    public function save()
    {
        $client_auto = ClientAuto::findOne($this->id);
        $client_auto->setAttributes($this->attributes);
        $client_auto->save();

        return $client_auto;
    }

}