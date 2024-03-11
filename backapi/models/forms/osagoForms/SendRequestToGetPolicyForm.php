<?php
namespace backapi\models\forms\osagoForms;

use common\helpers\DateHelper;
use common\models\Osago;
use common\models\OsagoRequest;
use common\models\Partner;
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
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne($this->id);
        if (!empty($osago->policy_pdf_url))
            throw new BadRequestHttpException('У этого осаго есть URL полис!');

        if ($osago->partner_id == Partner::PARTNER['kapital'])
            return $osago->partner_payment();

        $response_arr = OsagoRequest::sendRequest(OsagoRequest::URLS['get_policy_data'], $osago, ['order_id' => $osago->order_id_in_gross], false);
        if (is_array($response_arr) and array_key_exists('result', $response_arr) and !empty($response_arr['result']->pdf_url))
        {
            $osago->policy_number = $response_arr['result']->policy_number;
            $osago->policy_pdf_url = $response_arr['result']->pdf_url;
            $osago->begin_date = DateHelper::date_format($response_arr['result']->begin_date, 'Y-m-d', 'm.d.Y');
            $osago->end_date = DateHelper::date_format($response_arr['result']->end_date, 'Y-m-d', 'm.d.Y');
            $osago->status = Osago::STATUS['received_policy'];
            $osago->save();

            return $response_arr;
        }

        return $osago->get_policy_from_partner();
    }

}