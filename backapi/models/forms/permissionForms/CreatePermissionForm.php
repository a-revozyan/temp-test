<?php
namespace backapi\models\forms\permissionForms;

use mdm\admin\models\AuthItem;
use yii\base\Model;
use yii\rbac\Role;


class CreatePermissionForm extends Model
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

    public function save()
    {
        $model = new AuthItem(null);
        $model->type = Role::TYPE_PERMISSION;
        $model->setAttributes($this->attributes);
        $model->save();
        return $model;
    }
}