<?php
namespace backapi\models\forms\autobrandForms;

use common\models\Autobrand;
use common\models\Automodel;
use yii\base\Model;


class UpdateAutobrandForm extends Model
{
    public $id;
    public $name;
    public $order;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'status'], 'required'],
            [['name'], 'string'],
            [['id', 'order'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Autobrand::className(), 'targetAttribute' => ['id' => 'id']],
            [['status'], 'in', 'range' => Autobrand::status],
        ];
    }

    public function save()
    {
        $autobrand = Autobrand::findOne($this->id);
        $autobrand->setAttributes($this->attributes);
        $autobrand->save();

        return $autobrand;
    }

}