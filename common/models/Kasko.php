<?php

namespace common\models;

use common\helpers\DateHelper;
use common\helpers\GeneralHelper;
use common\services\TelegramService;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "kasko".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $tariff_id
 * @property int $autobrand_id
 * @property int $autocomp_id
 * @property int $year
 * @property float $price
 * @property string $autonumber
 * @property float $amount_uzs
 * @property float $amount_usd
 * @property string $begin_date
 * @property string $end_date
 * @property int $status
 * @property int $trans_id
 * @property int $created_at
 * @property string $insurer_name
 * @property string $insurer_address
 * @property string $insurer_phone
 * @property string $insurer_passport_series
 * @property string $insurer_passport_number
 * @property string $insurer_tech_pass_series
 * @property string $insurer_tech_pass_number
 * @property string $insurer_pinfl
 * @property string $address_delivery
 * @property bool $viewed
 * @property string $policy_number
 * @property int $policy_order
 * @property int $promo_id
 * @property float $promo_percent
 * @property float $promo_amount
 *
 * @property Partner $partner
 * @property Autocomp $autocomp
 * @property KaskoTariff $tariff
 * @property Transaction $trans
 * @property Warehouse $warehouse
 * @property KaskoFile $kaskoFile
 * @property Surveyer $surveyer
 *
 * @property int $surveyer_id
 * @property int $payed_date
 * @property int $step4_date
 *
 * @property int $surveyer_comment
 * @property int $processed_date
 * @property int $warehouse_id
 * @property int $bridge_company_id
 *
 * @property string $autobrand_name
 * @property string $automodel_name
 * @property string $autocomp_name
 *
 * @property int $agent_amount
 * @property int $surveyer_amount
 * @property string $uuid
 * @property int $f_user_id
 */
class Kasko extends \yii\db\ActiveRecord
{
    public $auto;
    public $price_coeff;
    public $promo_code;
    public $deadline_date;

    const STATUS = [
        "step1" => 1,
        "step2" => 2,
        "step3" => 3,
        "step4" => 4,
        "payed" => 5,
        "attached" => 6,
        "processed" => 7,
        "policy_generated" => 8,
        "canceled" => 9,
        'stranger' => 10,
    ];

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'autocomp';
        $fields[] = 'tariff';
        $fields[] = 'kaskoFile';
        $fields['begin_date']  = function ($model) {
            if ($model->begin_date == null)
                return null;
            return DateHelper::date_format($model->begin_date, 'Y-m-d', 'd.m.Y');
        };
        $fields['end_date']  = function ($model) {
            if ($model->end_date == null)
                return null;
            return DateHelper::date_format($model->end_date, 'Y-m-d', 'd.m.Y');
        };
        $fields['payed_date']  = function ($model) {
            if ($model->payed_date == null)
                return null;
            return date('d.m.Y H:i', $model->payed_date);
        };
        $fields['step4_date']  = function ($model) {
            if ($model->step4_date == null)
                return null;
            return date('d.m.Y H:i', $model->step4_date);
        };
        $fields['created_at']  = function ($model) {
            if ($model->created_at == null)
                return null;
            return date('d.m.Y H:i', $model->created_at);
        };
        $fields['deadline_date'] = function ($model) {
            if ($model->payed_date == null)
                return null;
            return date('d.m.Y H:i', strtotime('+1 day', $model->payed_date));
        };
        $fields['processed_date'] = function ($model) {
            if ($model->processed_date == null)
                return null;
            return date('d.m.Y H:i', $model->processed_date);
        };
        return $fields;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            // This is a new instance of modelClass, run your 'insert' code here.
            $this->uuid = UuidHelper::uuid();;
        }

        StatusHistory::create($this);

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at'], 'required', 'message' => Yii::t('app', 'Необходимо заполнить')],
            [['tariff_id', 'autocomp_id', 'year', 'status', 'trans_id', 'created_at'], 'default', 'value' => null],
            [['tariff_id', 'autocomp_id', 'year', 'status', 'trans_id', 'created_at', 'autobrand_id', 'promo_id', 'agent_amount', 'surveyer_amount', 'reason_id'], 'integer'],
            [['price', 'amount_uzs', 'amount_usd', 'promo_percent', 'promo_amount'], 'number'],
            [['begin_date', 'end_date', 'autobrand_name', 'automodel_name', 'autocomp_name', 'comment'], 'safe'],
            [['auto', 'autonumber', 'insurer_name', 'insurer_address', 'insurer_phone', 'insurer_passport_series', 'insurer_passport_number', 'insurer_tech_pass_series', 'insurer_tech_pass_number', 'insurer_pinfl', 'address_delivery', 'promo_code', 'uuid'], 'string', 'max' => 255],
            [['autocomp_id'], 'exist', 'skipOnError' => true, 'targetClass' => Autocomp::className(), 'targetAttribute' => ['autocomp_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
            [['trans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transaction::className(), 'targetAttribute' => ['trans_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tariff_id' => Yii::t('app', 'Tariff ID'),
            'autocomp_id' => Yii::t('app', 'Autocomp ID'),
            'year' => Yii::t('app', 'Year'),
            'price' => Yii::t('app', 'Price'),
            'autonumber' => Yii::t('app', 'Autonumber'),
            'amount_uzs' => Yii::t('app', 'Amount Uzs'),
            'amount_usd' => Yii::t('app', 'Amount Usd'),
            'begin_date' => Yii::t('app', 'Begin Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'status' => Yii::t('app', 'Status'),
            'trans_id' => Yii::t('app', 'Trans ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'insurer_name' => Yii::t('app', 'Insurer Name'),
            'insurer_address' => Yii::t('app', 'Insurer Address'),
            'insurer_phone' => Yii::t('app', 'Insurer Phone'),
            'insurer_passport_series' => Yii::t('app', 'Insurer Passport Series'),
            'insurer_passport_number' => Yii::t('app', 'Insurer Passport Number'),
            'insurer_tech_pass_series' => Yii::t('app', 'Insurer Tech Pass Series'),
            'insurer_tech_pass_number' => Yii::t('app', 'Insurer Tech Pass Number'),
            'insurer_pinfl' => Yii::t('app', 'Insurer Pinfl'),
            'deadline_date' => Yii::t('app', 'Deadline date'),
        ];
    }

    public static function with_pagination($where)
    {
        $query = Kasko::find()->where($where)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'defaultPageSize' => 10
        ]);
        $models = $query->offset($pages->defaultPageSize * ((Yii::$app->request->get()['page'] ?? 1) - 1))
            ->limit($pages->limit)
            ->all();

        return [
            'models' => $models,
            'pages' => $pages,
        ];
    }

    /**
     * Gets query for [[Autocomp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutocomp()
    {
        return $this->hasOne(Autocomp::className(), ['id' => 'autocomp_id']);
    }

    /**
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(KaskoTariff::className(), ['id' => 'tariff_id']);
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }

    /**
     * Gets query for [[Surveyer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyer()
    {
        return $this->hasOne(Surveyer::className(), ['id' => 'surveyer_id']);
    }

    public function getFUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    /**
     * Gets query for [[KaskoFile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskoFile()
    {
        return $this->hasMany(KaskoFile::className(), ['kasko_id' => 'id']);
    }

    /**
     * Gets query for [[Warehouse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * Gets query for [[Trans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrans()
    {
        return $this->hasOne(Transaction::className(), ['id' => 'trans_id']);
    }

    public function getReason()
    {
        return $this->hasOne(Reason::className(), ['id' => 'reason_id']);
    }

    public function statusToBackBeforePayment()
    {
        $warehouse = $this->warehouse;
        if ($warehouse)
        {
            $warehouse->status = '0';
            $warehouse->save();
        }

        $this->status = self::STATUS['canceled'];
        $this->warehouse_id = null;
        $this->agent_amount = null;
        $this->save();
    }

    public static function getYearsList() {
        $currentYear = date('Y');
        $yearFrom = 2011;
        $yearsRange = range($yearFrom, $currentYear);
        return array_reverse(array_combine($yearsRange, $yearsRange));
    }

    public static function ceiling($number, $significance = 1)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }

    public function calc()
    {
        if($this->autobrand_id == 0) {
            $price = 2000000000 * $this->price_coeff;
        } else {
            $price = $this->autocomp->price * $this->price_coeff;

            $price = self::getAutoRealPrice($price, $this->year);
        }

        $this->price = $price;

        $result = [];

        if($this->tariff_id) {
            $tariffs = [KaskoTariff::findOne([$this->tariff_id])];
        } else {
            $tariffs = KaskoTariff::find()
                ->orderBy('partner_id')
                ->all();
        }

        $usd = Currency::getUsdRate();

        $promo = Promo::find()->where(['code' => $this->promo_code])->one();

        if($promo) {
            $this->promo_id = $promo->id;
            $this->promo_percent = $promo->percent;
            $promo_percent = $promo->percent;
        } else {
            $promo_percent = 0;
            $this->promo_percent = 0;
        }

        foreach($tariffs as $tariff) {
            $partner_product = PartnerProduct::find()->where(['product_id' => 2, 'partner_id' => $tariff->partner_id])->one();

            if(!$partner_product) {
                $partner_product = new PartnerProduct();
                $partner_product->partner_id = $tariff->partner_id;
                $partner_product->percent = 0;
            }

            if($tariff) {
                if($tariff->amount_kind == 'P'){
                    $amount = round($price * $tariff->amount / 100);
                } else {
                    $amount = $tariff->amount;
                }
                $amount_with_margin = (($promo_percent + 100) / 100) * $amount;

                $result[] = [
                    'partner' => $tariff->partner,
                    'tariff_id' => $tariff->id,
                    'margin' => $promo_percent,
                    'without_margin' => self::ceiling($amount, 1000),
                    'amount' => self::ceiling($amount_with_margin, 1000),
                    'amount_usd' => round(self::ceiling($amount, 1000) / $usd, 2),
                    'risks' => $tariff->kaskoTariffRisks,
                    'star' => $partner_product->star
                ];
            }
        }

        return $result;
    }

    public function calc2($selected_price = null, $is_islomic = 0, $auto_risk_type_id = null, $car_accessory_ids = [], $car_accessory_amounts = [])
    {
        if($this->autobrand_id == 0) {
            $price = 2000000000 * $this->price_coeff;
        } else {
            $price = $this->autocomp->price * $this->price_coeff;

            $price = Kasko::getAutoRealPrice($price, $this->year);
        }

        $this->price = $price;

        $result = [];

        if($this->tariff_id) {
            $tariffs = [KaskoTariff::findOne([$this->tariff_id])];
        } else {
            $tariffs = KaskoTariff::find()
                ->where(['is_islomic' => $is_islomic])
                ->leftJoin(
                    [
                        'auto_risk_kasko_tariff' => (new Query())
                            ->select(['count(id) as auto_risk_type_count', 'kasko_tariff_id'])
                            ->where(['auto_risk_type_id' => $auto_risk_type_id])
                            ->groupBy('kasko_tariff_id')
                            ->from('auto_risk_kasko_tariff')
                    ],
                    'kasko_tariff.id = auto_risk_kasko_tariff.kasko_tariff_id'
                )
                ->leftJoin('partner', 'partner.id = kasko_tariff.partner_id')
                ->andWhere(['>', 'auto_risk_type_count' , '0'])
                ->andWhere(['partner.status' => 1])
                ->andWhere([
                    'or',
                    ['<=', 'kasko_tariff.min_price', $selected_price],
                    ['kasko_tariff.min_price' => null],
                ])
                ->andWhere([
                    'or',
                    ['>=', 'kasko_tariff.max_price', $selected_price],
                    ['kasko_tariff.max_price' => null],
                ])
                ->andWhere([
                    'or',
                    ['<=', 'kasko_tariff.min_year', $this->year],
                    ['kasko_tariff.min_year' => null],
                ])
                ->andWhere([
                    'or',
                    ['>=', 'kasko_tariff.max_year', $this->year],
                    ['kasko_tariff.max_year' => null],
                ])
                ->orderBy('partner_id')
                ->all();
        }

        $usd = Currency::getUsdRate();

        $promo = Promo::find()->where(['code' => $this->promo_code])->one();
        if($promo) {
            $this->promo_id = $promo->id;
            $this->promo_percent = $promo->percent;
            $promo_percent = $promo->percent;
        } else {
            $promo_percent = 0;
            $this->promo_percent = 0;
        }

        foreach($tariffs as $tariff) {
            $partner_product = PartnerProduct::find()->where(['product_id' => 2, 'partner_id' => $tariff->partner_id])->one();

            if(!$partner_product) {
                $partner_product = new PartnerProduct();
                $partner_product->partner_id = $tariff->partner_id;
                $partner_product->percent = 0;
            }

            if($tariff) {

                $amount = $tariff->amount;
                if ($is_islomic == 1 and $tariff_islomic_amount = TariffIslomicAmount::find()
                        ->where(['kasko_tariff_id' => $tariff->id, 'auto_risk_type_id' => $auto_risk_type_id])->one())
                    $amount = $tariff_islomic_amount->amount;
                elseif($is_islomic == 1)
                    continue;

                if($tariff->amount_kind == 'P')
                    $amount = $selected_price * $amount / 100;

                $lang = GeneralHelper::lang_of_local();

                $tariff_has_car_accessories = true;
                foreach ($car_accessory_ids as $key => $car_accessory_id) {
                    if (
                        !(
                            $tariff_car_accessory = TariffCarAccessoryCoeff::find()
                                ->where(['tariff_id' => $tariff->id])
                                ->andWhere(['car_accessory_id' => $car_accessory_id])->with('carAccessory')->one()
                            and $carAccessory = $tariff_car_accessory->carAccessory
                        )
                    )
                    {
                        $tariff_has_car_accessories = false;
                        break;
                    }

                    if (
                        $car_accessory_amounts[$key] > $carAccessory->amount_min
                        and $car_accessory_amounts[$key] < $carAccessory->amount_max
                    )
                        $amount += $car_accessory_amounts[$key] * $tariff_car_accessory->coeff / 100;
                    else
                        throw new BadRequestHttpException(Yii::t('app', $tariff_car_accessory->carAccessory->{"name_" . $lang} . " uchun narx " . $carAccessory->amount_min . " dan katta va " . $carAccessory->amount_max) . " dan kichik bo'lishi kerak.");

                }
                if (!$tariff_has_car_accessories)
                    continue;

                $amount_with_margin = (($promo_percent + 100) / 100) * $amount;

                $result[] = [
                    'tariff_id' => $tariff->id,
                    'partner' => $tariff->partner->name,
                    'partner_image' => GeneralHelper::env('front_website_send_request_url') . "/uploads/partners/" . $tariff->partner->image,
                    'tariff_file' => !empty($tariff->file) ? GeneralHelper::env('backend_project_website') . '/' . $tariff->file : null,
                    'tariff' => $tariff->name,
                    'risks' => $tariff->kaskoRisks,
                    'amount_without_margin' => self::ceiling($amount, 1000),
                    'amount_usd' => round(self::ceiling($amount, 1000) / $usd, 2),
                    'amount' => self::ceiling($amount_with_margin, 1000),
//                    'amount_usd' => round(self::ceiling($amount, 1000) / $usd, 2),
                    'star' => $partner_product->star,
                    'franchise' => $tariff->{"franchise_" . $lang},
                    'only_first_risk' => $tariff->{"only_first_risk_" . $lang},
                    'is_conditional' => $tariff->is_conditional
                ];
            }
        }

        return $result;
    }


    public function setGrossPolicyNumber() {
        if($this->trans->status == 2) {
            $last_order = self::find()->where(['partner_id' => 1])->max("policy_order");

            if(is_null($last_order)) {
                $last_order = 0;
            }

            $this->policy_number = 'NKK ';

            $this->policy_order = $last_order + 1;

            $length = strlen($this->policy_order);

            if($length < 7) {
                for($i = 0; $i < 7-$length; $i++) {
                    $this->policy_number .= '0';
                }
                $this->policy_number .= $this->policy_order;
            } else {
                $this->policy_number = $this->policy_order;
            }
            $this->save();
        }
    }

    public function saveAfterPayed()
    {
        $warehouse = Warehouse::findOne(['partner_id' => $this->partner_id, 'product_id' => Product::products['kasko'], 'status' => 0]);
        if (is_null($warehouse))
        {
            $warehouse = new Warehouse();
            $warehouse->product_id = Product::products['kasko'];
            $warehouse->partner_id = $this->partner_id;
        }
        else
            $this->policy_number = $warehouse->series . " " . $warehouse->number;

        $warehouse->status = (string)Warehouse::STATUS['paid'];
        $warehouse->save();

        $this->status = self::STATUS['payed'];
        $this->payed_date = time();
        $this->warehouse_id = $warehouse->id;
        $this->save();

        TelegramService::send($this);

        return true;
    }

    public function getOldKaskoRisk()
    {
        return $this->hasMany(OldKaskoRisk::className(), ["id" => "old_kasko_risk_id"])->viaTable('kasko_old_kasko_risk', ['kasko_id' => "id"]);
    }

    public static function getShortArrCollection($kaskos)
    {
        $_kaskos = [];
        foreach ($kaskos as $kasko) {
            $_kaskos[] = $kasko->getShortArr();
        }
        return $_kaskos;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'autonumber' => $this->autonumber,
            'insurer_tech_pass_series' => $this->insurer_tech_pass_series,
            'insurer_tech_pass_number' => $this->insurer_tech_pass_number,
            'insurer_passport_series' => $this->insurer_passport_series,
            'insurer_passport_number' => $this->insurer_passport_number,
            'insurer_address' => $this->insurer_address,
            'insurer_name' => $this->insurer_name,
            'insurer_phone' => $this->insurer_phone,
            'insurer_pinfl' => $this->insurer_pinfl,
            'amount_uzs' => $this->amount_uzs,
            'price' => $this->price,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'year' => $this->year,
            'status' => $this->status,
            'promo_amount' => $this->promo_amount,
            'promo_id' => $this->promo_id,
            'autocomp' => !is_null($this->autocomp) ? $this->autocomp->getWithParentArr() : null,
            'tariff' => !is_null($this->tariff) ? $this->tariff->getWithPartnerNameArr() : null,
        ];
    }

    public function getFullArr()
    {
        return [
            'id' => $this->id,
            'autonumber' => $this->autonumber,
            'insurer_tech_pass_series' => $this->insurer_tech_pass_series,
            'insurer_tech_pass_number' => $this->insurer_tech_pass_number,
            'insurer_passport_series' => $this->insurer_passport_series,
            'insurer_passport_number' => $this->insurer_passport_number,
            'insurer_address' => $this->insurer_address,
            'insurer_name' => $this->insurer_name,
            'insurer_phone' => $this->insurer_phone,
            'insurer_pinfl' => $this->insurer_pinfl,
            'amount_uzs' => $this->amount_uzs,
            'price' => $this->price,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
            'year' => $this->year,
            'status' => $this->status,
            'promo_amount' => $this->promo_amount,
            'promo_id' => $this->promo_id,
            'autocomp' => !is_null($this->autocomp) ? $this->autocomp->getWithParentArr() : null,
            'tariff' => !is_null($this->tariff) ? $this->tariff->getWithPartnerNameArr() : null,
            'warehouse' => !is_null($this->warehouse) ? $this->warehouse->getShortArr() : null,
            'kaskoFile' => !empty($this->kaskoFile) ? KaskoFile::getFullArrCollection($this->kaskoFile) : [],
            'surveyer' => !is_null($this->surveyer) ? $this->surveyer->getShortArr() : null,
        ];
    }

    public static function getAutoRealPrice($price, $year): float
    {
        return round($price * pow(0.97, date('Y') - $year), -3);
    }
}
