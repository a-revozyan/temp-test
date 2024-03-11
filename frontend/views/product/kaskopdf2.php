<?php
use common\helpers\GeneralHelper;use common\models\Kasko;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$model = Kasko::findOne([$id]);

if($model->price == $model->amount_uzs * 100 / $model->tariff->amount) {
    $model->price = $model->amount_uzs * 100 / $model->tariff->amount;
    $model->save();
}

$qrCode = new QrCode(GeneralHelper::env('frontend_project_website').'/kaskoapi/pdf-path-of-tariff?id=' . $model->tariff_id);
$qrCode2 = new QrCode(GeneralHelper::env('front_website_url') . '/policy/' . $model->uuid);
$output = new Output\Svg();

?>
<div class=" pdf" >
    <div class="row"  style="margin: 0; padding: 24; padding-bottom: 36px; background: #F4F6F8">
        <div class="header" style="float: left; width: 70%; ">
            <h2 style="font-size:18px;font-weight: bold; margin: 0; padding: 0">
                Полис добровольного страхования <br>
                транспортных средств
            </h2>
        </div>
        <div style="text-align: right;float: left; width: 17%;">
            <img class="img img-responsive" src="<?= Yii::getAlias('@frontend/web/uploads/partners/') . $model->partner->image ?>" />
        </div>
        <div style="clear: both"></div>
    </div>
    <table class="table " style="margin: 24px; margin-bottom: 0; margin-top: 39px; border: none">
        <tr>
            <th colspan=2 style="border: none"><?=$model->policy_number?> <br>Данные страхователя</th>
        </tr>
        <tr>
            <td style="border: none; width: 40%">
                <small>Страхователь</small>
                <p style="text-transform: uppercase;"><?=$model->insurer_name?></p>
            </td>
            <td style="border: none; width: 30%">
                <small class="text-bold m-0">Контактный номер</small>
                <p class="upper m-0"><?= "+998" . $model->insurer_phone?></p>
            </td>
            <td style="border: none; width: 30%">
                <small class="text-bold m-0">Серия и номер паспорта</small>
                <p class="upper m-0"><?=$model->insurer_passport_series . $model->insurer_passport_number?></p>
            </td>
        </tr>
        <tr>
            <td>
                <small>PNFL</small>
                <p style="text-transform: uppercase;"><?=$model->insurer_pinfl?></p>
            </td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <div style="margin-right: 24px; margin-left: 24px">
        <hr style="margin: 0;">
    </div>
    <table class="table  mt-0 mb-0" style="margin: 24px; margin-bottom: 0; margin-top: 0; border: none">
        <tr>
            <th colspan=3 style="border: none;">Данные транспортного средства</th>
        </tr>
        <tr>
            <td style="border: none; width: 40%">
                <small class='text-bold' style='margin-bottom: 5px;'>Производитель</small>
                <p class="upper"><?=$model->autobrand_name?></p>
            </td>
            <td  style="border: none; width: 30%">
                <small class='text-bold' style='margin-bottom: 5px;'>Модель автомобиля</small>
                <p class="upper"><?=$model->automodel_name?> <?=$model->autocomp->name?></p>
            </td>
            <td style="border: none; width: 30%">
                <small class='text-bold' style='margin-bottom: 5px;'>Год выпуска</small>
                <p class="upper"><?=$model->year?></p>
            </td>
        </tr>
        <tr>
            <td style="border: none; width: 40%">
                <?php
                $price = $model->autocomp->price;
                $price = Kasko::getAutoRealPrice($price, $model->year);
                ?>
                <p class='text-bold' style='margin-bottom: 5px;'>Полная стоимость автомобиля</p>
                <p class="upper"><?=number_format(round($price, -3), 0,","," ")?> сум</p>
            </td>
            <td style="border: none; width: 30%">
                <p class='text-bold' style='margin-bottom: 5px;'>Государственный номер</p>
                <p class="upper"><?=$model->autonumber?></p>
            </td>
            <td style="border: none;  width: 30%">
                <p class='text-bold' style='margin-bottom: 5px;'>Тех паспорт №</p>
                <p class="upper"><?=$model->insurer_tech_pass_series?> <?=$model->insurer_tech_pass_number?></p>
            </td>
        </tr>
    </table>
    <div style="margin-right: 24px; margin-left: 24px">
        <hr style="margin: 0;">
    </div>
    <table class="table  mt-0 mb-0" style="margin-bottom: 0; margin-left: 24px; margin-right: 24px; border: none;">
        <tr style="border-top: none;">
            <th colspan=2  style="border: none">Условия страхования</th>
        </tr>
        <tr>
            <td  style="border: none;  width: 40%">
                <small class='text-bold' style='margin-bottom: 5px;'>Период страхования</small>
                <p class="upper">с <?=date('d.m.Y', strtotime($model->begin_date)) . ' до ' .date('d.m.Y', strtotime("+364 day", strtotime($model->begin_date)));?></p>
            </td style="border: none">
            <td style="border: none;  width: 30%">
                <small class='text-bold' style='margin-bottom: 5px;'>Общее страховое покрытие</small>
                <p class="upper"><?=number_format($model->price, 0,","," ")?> сум</p>
            </td>
            <td style="border: none;  width: 30%">
                <small class='text-bold' style='margin-bottom: 5px;'>Общая страховая премия</small>
                <p class="upper"><?=number_format($model->amount_uzs - $model->promo_amount, 0,","," ")?> сум</p>
            </td>
        </tr>
        <tr>
            <td  style="border: none;  width: 40%; vertical-align:top">
                <small class='text-bold' style='margin-bottom: 5px;'>Франшиза</small>
                <p class="upper"><?php echo empty($model->tariff->franchise_ru) ? "Нет" : "Есть"  ?></p>
            </td style="border: none">
            <td style="border: none;  width: 30%; height: 112px; vertical-align:top" colspan="2">
                <small class='text-bold' style='margin-bottom: 5px;'>Условия франшизы</small>
                <p class="upper"><?= $model->tariff->franchise_ru ?></p>
            </td>
        </tr>
    </table>
    <div class="">
        <div style="float: right; width: 40%; margin: 35px 0">
            <div>
                <div style="width: 50%; float: left;">
                    <figure>
                        <div style="margin: 0 10px; text-align: center;"><?= str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 100, 'white', 'black')); ?></div>
                        <figcaption style="text-align: center;font-size:12px;">Правила страхования </figcaption>
                    </figure>
                </div>
                <div style="width: 50%; float: right;">
                    <figure>
                        <div style="margin: 0 10px; text-align: center;">
                            <?= str_replace('<?xml version="1.0"?>', '', $output->output($qrCode2, 100, 'white', 'black')); ?>
                        </div>
                        <figcaption style="text-align: center;font-size:12px;">Данные о полисе</figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-right: 24px; margin-left: 24px">
        <h3 style="text-align: left;font-size: 14px; color: black">Памятка клиенту</h3>
        <h4 style="font-size: 12px; color: black">Попали в ДТП или произошло иное повреждение с автомобилем?</h4>

        <table style="font-size: 12px; margin-left: 0; padding-left: 0; list-style-type: none">
            <tr>
                <td style="line-height: 16px;width: 22px; height: 22px; text-align: center; background: url('<?= Yii::getAlias('@frontend/web/img/pdf/rounded.png') ?>'); background-size: contain; background-repeat: no-repeat; background-position: 0 50%"> 1 </td>
                <td>Заглушите двигатель. Вытащите ключ из замка зажигания и уберите его в карман. Включите стояночный тормоз и аварийную световую сигнализацию.</td>
            </tr>
            <tr>
                <td style="width: 22px; height: 22px; text-align: center; background: url('<?= Yii::getAlias('@frontend/web/img/pdf/rounded.png') ?>'); background-size: contain; background-repeat: no-repeat; background-position: 0 50%">2</td>
                <td>Если есть пострадавшие, срочно вызовите скорую по телефонам 103. До ее приезда не оставляйте пострадавших.</td>
            </tr>
            <tr>
                <td style="line-height: 16px;width: 22px; height: 22px; text-align: center; background: url('<?= Yii::getAlias('@frontend/web/img/pdf/rounded.png') ?>'); background-size: contain; background-repeat: no-repeat; background-position: 0 50%">3</td>
                <td>Поставьте знак аварийной остановки. Перед тем как выйти из автомобиля, наденьте светоотражающий жилет. В соответствии с ПДД знак аварийной остановки должен быть установлен на расстоянии 15 метров от вашей машины в населенном пункте и в 30 метрах вне населенного пункта.</td>
            </tr>
            <tr>
                <td style="width: 22px; height: 22px; text-align: center; background: url('<?= Yii::getAlias('@frontend/web/img/pdf/rounded.png') ?>'); background-size: contain; background-repeat: no-repeat; background-position: 0 50%">4</td>
                <td>Не передвигайте автомобиль со своих мест.</td>
            </tr>
            <tr>
                <td style="width: 22px; height: 22px; text-align: center; background: url('<?= Yii::getAlias('@frontend/web/img/pdf/rounded.png') ?>'); background-size: 22px 22px; background-repeat: no-repeat; background-position: 0 50%">5</td>
                <td>Позвоните в страховую компанию для получения помощи согласно страховому полису.</td>
            </tr>
        </table>

    </div>

    <div style="box-sizing: border-box; background: #F4F6F8; padding-left: 24px; padding-right: 24px; margin-top: 2px">
        <table style="width: 100%">
            <tr>
                <td><?= date('M d, Y', strtotime($model->begin_date)) . ' - ' . date('M d, Y', strtotime($model->end_date)) ?></td>
                <td style="text-align: right"><img style="width: 103px" src="<?= Yii::getAlias('@frontend/web/img/pdf/logo_su.svg') ?>" alt=""></td>
            </tr>
        </table>
    </div>

</div>