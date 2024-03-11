<?php
namespace backapi\models\forms\autoRiskTypeForms;

use common\models\AutoRiskType;
use yii\base\Model;


class CreateAutoRiskTypeForm extends Model
{
    public $name;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => AutoRiskType::STATUS],
        ];
    }

    public function save()
    {
        $auto_risk_type = new AutoRiskType();
        $auto_risk_type->name = $this->name;
        $auto_risk_type->status = $this->status;
        $auto_risk_type->save();

        return $auto_risk_type;
    }

}