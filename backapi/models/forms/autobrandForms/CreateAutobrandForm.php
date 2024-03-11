<?php
namespace backapi\models\forms\autobrandForms;

use common\models\Autobrand;
use yii\base\Model;

class CreateAutobrandForm extends Model
{
    public $name;
    public $order;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['name'], 'string'],
            [['order', 'status'], 'integer'],
            [['status'], 'in', 'range' => Autobrand::status],
        ];
    }

    public function save()
    {
        $autobrand = new Autobrand();
        $autobrand->name = $this->name;
        $autobrand->order = $this->order;
        $autobrand->status = $this->status;
        $autobrand->save();

        return $autobrand;
    }

}