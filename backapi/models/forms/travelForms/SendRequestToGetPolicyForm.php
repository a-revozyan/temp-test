<?php
namespace backapi\models\forms\travelForms;

use common\helpers\DateHelper;
use common\models\Travel;
use common\models\OsagoRequest;
use yii\base\Model;
use yii\web\BadRequestHttpException;


class SendRequestToGetPolicyForm extends Model
{
    public $id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Travel::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        return null;
        $travel = Travel::findOne($this->id);
        if (!empty($travel->policy_pdf_url))
            throw new BadRequestHttpException('У этого travel есть URL полис!');

        $response_arr = OsagoRequest::sendTravelRequest(OsagoRequest::URLS['get_policy_data'], $travel, ['order_id' => $travel->order_id_in_gross], false);
        if (is_array($response_arr) and array_key_exists('result', $response_arr) and !empty($response_arr['result']->pdf_url))
        {
            $travel->policy_number = $response_arr['result']->policy_number;
            $travel->policy_pdf_url = $response_arr['result']->pdf_url;
            $travel->begin_date = DateHelper::date_format($response_arr['result']->begin_date, 'Y-m-d', 'm.d.Y');
            $travel->end_date = DateHelper::date_format($response_arr['result']->end_date, 'Y-m-d', 'm.d.Y');
            $travel->status = Travel::STATUSES['received_policy'];
            $travel->save();

            return $response_arr;
        }

        return $travel->send_save_to_partner_system(1, 0, true);
    }

}