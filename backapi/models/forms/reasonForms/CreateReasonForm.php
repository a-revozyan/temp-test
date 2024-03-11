<?php
namespace backapi\models\forms\reasonForms;

use common\models\Reason;
use yii\base\Model;

class CreateReasonForm extends Model
{
    public $name;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => Reason::STATUS],
        ];
    }

    public function save()
    {
        $reason = new Reason();
        $reason->name = $this->name;
        $reason->status = $this->status;
        $reason->save();

        return $reason;
    }

}