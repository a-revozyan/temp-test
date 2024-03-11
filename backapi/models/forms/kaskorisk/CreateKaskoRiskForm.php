<?php
namespace backapi\models\forms\kaskorisk;

use common\models\Autocomp;
use common\models\KaskoRisk;
use common\models\KaskoRiskCategory;
use yii\base\Model;


class CreateKaskoRiskForm extends Model
{
    public $amount;
    public $name_ru;
    public $name_en;
    public $name_uz;
    public $category_id;
    public $description_ru;
    public $description_uz;
    public $description_en;
    public $show_desc;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_uz', 'name_en', 'amount', 'show_desc'], 'required'],
            [['category_id'], 'integer'],
            [['name_ru', 'name_uz', 'name_en', 'description_en', 'description_uz', 'description_ru'], 'string'],
            [['name_ru', 'name_uz', 'name_en', 'description_en', 'description_uz', 'description_ru'], 'filter', 'filter' => 'trim'],
            [['show_desc'], 'integer', 'min' => 0, 'max' => 1],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoRiskCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    public function save()
    {
        $kaskorisk = new KaskoRisk();
        $kaskorisk->amount = $this->amount;
        $kaskorisk->name_ru = $this->name_ru;
        $kaskorisk->name_en = $this->name_en;
        $kaskorisk->name_uz = $this->name_uz;
        $kaskorisk->description_ru = $this->description_ru;
        $kaskorisk->description_uz = $this->description_uz;
        $kaskorisk->description_en = $this->description_en;
        $kaskorisk->category_id = empty($this->category_id) ? null : $this->category_id;
        $kaskorisk->show_desc = $this->show_desc;

        $kaskorisk->save();

        return $kaskorisk;
    }

}