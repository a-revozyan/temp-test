<?php
namespace backapi\models\forms\carAccessoryForms;

use common\models\CarAccessory;
use yii\base\Model;


class UpdateCarAccessoryForm extends Model
{
    public $name_ru;
    public $name_uz;
    public $name_en;
    public $description_ru;
    public $description_uz;
    public $description_en;
    public $amount_min;
    public $amount_max;
    public $id;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name_ru', 'name_en', 'name_uz', 'amount_min', 'amount_max' , 'id'], 'required'],
            [['name_ru', 'name_en', 'name_uz', 'description_ru', 'description_en', 'description_uz'], 'string'],
            [['amount_min', 'amount_max'], 'double'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => CarAccessory::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save(): ?CarAccessory
    {
        $car_accessory = CarAccessory::findOne($this->id);
        $car_accessory->setAttributes($this->attributes);
        $car_accessory->save();

        return $car_accessory;
    }

}