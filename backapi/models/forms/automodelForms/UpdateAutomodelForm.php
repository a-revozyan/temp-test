<?php
namespace backapi\models\forms\automodelForms;

use common\models\Autobrand;
use common\models\Automodel;
use common\models\AutoRiskType;
use yii\base\Model;


class UpdateAutomodelForm extends Model
{
    public $id;
    public $autobrand_name;
    public $name;
    public $order;
    public $auto_risk_type_id;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autobrand_name', 'name', 'id', 'status'], 'required'],
            [['autobrand_name', 'name'], 'string'],
            [['autobrand_name', 'name'], 'filter', 'filter' => 'trim'],
            [['auto_risk_type_id', 'order'], 'integer'],
            [['auto_risk_type_id', 'order'], 'default', 'value' => null,  "skipOnEmpty" =>false],
            [['auto_risk_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AutoRiskType::className(), 'targetAttribute' => ['auto_risk_type_id' => 'id']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Automodel::className(), 'targetAttribute' => ['id' => 'id']],
            [['status'], 'in', 'range' => Automodel::status],
        ];
    }

    public function save()
    {
        $automodel = Automodel::findOne($this->id);
        $automodel->name = $this->name;
        $automodel->order = $this->order;
        $automodel->status = $this->status;
        $automodel->auto_risk_type_id = $this->auto_risk_type_id;

        if (!$autobrand = Autobrand::find()->where(['name' => $this->autobrand_name])->one())
        {
            $autobrand = new Autobrand();
            $autobrand->name = $this->autobrand_name;
            $autobrand->save();
        }

        $automodel->autobrand_id = $autobrand->id;
        $automodel->save();

        return $automodel;
    }

}