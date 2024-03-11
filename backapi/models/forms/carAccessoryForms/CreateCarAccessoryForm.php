<?php
namespace backapi\models\forms\carAccessoryForms;

use common\models\CarAccessory;
use yii\base\Model;


class CreateCarAccessoryForm extends Model
{
    public $name_ru;
    public $name_uz;
    public $name_en;
    public $description_ru;
    public $description_uz;
    public $description_en;
    public $amount_min;
    public $amount_max;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_en', 'name_uz', 'amount_min', 'amount_max'], 'required'],
            [['name_ru', 'name_en', 'name_uz', 'description_ru', 'description_en', 'description_uz'], 'string'],
            [['amount_min', 'amount_max'], 'double'],
        ];
    }

    public function save()
    {
        $car_accessory = new CarAccessory();
        $car_accessory->setAttributes($this->attributes);
        $car_accessory->save();

        return $car_accessory;
    }

}