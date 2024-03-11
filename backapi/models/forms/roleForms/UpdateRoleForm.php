<?php
namespace backapi\models\forms\roleForms;

use common\models\Autobrand;
use common\models\Automodel;
use mdm\admin\models\AuthItem;
use yii\base\Model;


class UpdateRoleForm extends Model
{
    public $name;
    public $ruleName;
    public $description;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'ruleName', 'description'], 'string']
        ];
    }

    /**
     * @param AuthItem $model
     * @return AuthItem $model
     */
    public function save($model)
    {
        $model->setAttributes($this->attributes);
        $model->save();

        return $model;
    }

}