<?php
namespace backapi\models\forms\carInspectionForms;

use common\models\CarInspection;
use common\models\Partner;
use common\models\User;
use yii\base\Model;

class CreateCarInspectionForm extends Model
{
    public $partner_id;
    public $tex_pass_series;
    public $tex_pass_number;
    public $autonumber;
    public $phone;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autonumber', 'phone', 'partner_id', 'tex_pass_series', 'tex_pass_number'], 'required'],
            [['autonumber', 'phone', 'tex_pass_series', 'tex_pass_number'], 'string', 'max' => 255],
            [['phone'], 'filter', 'filter' => 'trim'],
            [['phone'], 'match', 'pattern' => '/[9]{2}[8][0-9]{2}[0-9]{3}[0-9]{2}[0-9]{2}$/', 'message'=>'phone format is not correct'],
            [['partner_id'], 'integer'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id'],
                'filter' => function($query){
                    return $query->leftJoin('f_user', 'partner.f_user_id = f_user.id')
                        ->andWhere([
                            'f_user.role' => User::ROLES['partner']
                        ]);
                }
            ],
        ];
    }

    public function save($created_by_admin = false)
    {
        return CarInspection::create($this, $created_by_admin);
    }

}