<?php
namespace backapi\models\forms\autoRiskTypeForms;

use common\models\AutoRiskType;
use yii\base\Model;


class UpdateAutoRiskTypeForm extends Model
{
    public $id;
    public $name;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => AutoRiskType::className(), 'targetAttribute' => ['id' => 'id']],
            [['name', 'id', 'status'], 'required'],
            [['status'], 'in', 'range' => AutoRiskType::STATUS],
        ];
    }

    public function save()
    {
        $auto_risk_type = AutoRiskType::findOne($this->id);
        $auto_risk_type->name = $this->name;
        $auto_risk_type->status = $this->status;
        $auto_risk_type->save();

        return $auto_risk_type;
    }

}