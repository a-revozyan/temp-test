<?php
namespace backapi\models\forms\carInspectionForms;

use common\models\CarInspection;
use yii\base\Model;

class UpdateCarInspectionForm extends Model
{
    public $id;
    public $runway;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'runway'], 'required'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => CarInspection::className(), 'targetAttribute' => ['id' => 'id']],
            [['runway'], 'integer'],
        ];
    }

    public function save()
    {
       $car_inspection = CarInspection::findOne($this->id);
       $car_inspection->runway = $this->runway;
       $car_inspection->save();

       return $car_inspection;
    }

}