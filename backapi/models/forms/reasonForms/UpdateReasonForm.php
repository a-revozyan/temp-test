<?php
namespace backapi\models\forms\reasonForms;

use common\models\Reason;
use yii\base\Model;


class UpdateReasonForm extends Model
{
    public $id;
    public $name;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['name'], 'string'],
            [['id', 'status'], 'integer'],
            [['status'], 'in', 'range' => Reason::STATUS],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Reason::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $reason = Reason::findOne($this->id);
        $reason->setAttributes($this->attributes);
        $reason->save();

        return $reason;
    }

}