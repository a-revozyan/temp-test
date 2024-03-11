<?php
use common\helpers\GeneralHelper;use common\models\Kasko;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;


$model = Kasko::findOne([$id]);

if($model->price == $model->amount_uzs * 100 / $model->tariff->amount) {
    $model->price = $model->amount_uzs * 100 / $model->tariff->amount;
    $model->save();
}

$qrCode = new QrCode( GeneralHelper::env('frontend_project_website').'/kaskoapi/pdf-path-of-tariff?id=' . $model->tariff_id);
$qrCode2 = new QrCode(GeneralHelper::env('front_website_url') . '/policy/' . $model->uuid);
$output = new Output\Svg();

?>
<div class="container-fluid pdf">
    <div class="row"  style="margin: 0;">
        <div class="header" style="float: left; width: 80%; padding-left: 15px;">
            <h2 style="font-size:18px;">
            <?php
            if($model->tariff_id == 2) :
                ?>
                Полис добровольного страхования транспортных средств «AVTOVIP»
            <?php else : ?>
                Полис добровольного страхования транспортных средств «PRESTIGE»
            <?php
            endif;
            ?></h2>
        </div>
        <div style="text-align: right;float: left; width: 17%;">
            <img class="img img-responsive" src="<?= Yii::getAlias('@frontend/web/img/pdf/logo.png') ?>" />
        </div>
        <div style="clear: both"></div>
    </div>
    <table class="table table-bordered" style="margin-bottom: 0; margin-top: 15px;">
        <tr>
            <th colspan=3>Данные страхователя</th>
        </tr>
        <tr>
            <td>
                <p style="margin-bottom: 20px;">Страхователь</p>
                <p style="text-transform: uppercase;"><?=$model->insurer_name?></p>
            </td>
            <td>
                <p class="text-bold m-0">Контактный номер</p>
                <p class="upper m-0"><?=$model->insurer_phone?></p>
            </td>
            <td>
                <p class="text-bold m-0">PNFL</p>
                <p class="upper m-0"><?=$model->insurer_pinfl?></p>
            </td>
        </tr>
    </table>
    <table class="table table-bordered mt-0 mb-0" style="margin-bottom: 0;">
        <tr style="border-top: none;">
            <th colspan=3>Данные транспортного средства</th>
        </tr>
        <tr>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Производитель</p>
                <p class="upper"><?=$model->autocomp->automodel->autobrand->name?></p>
            </td>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Модель автомобиля</p>
                <p class="upper"><?=$model->autocomp->automodel->name?> <?=$model->autocomp->name?></p>
            </td>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Год выпуска</p>
                <p class="upper"><?=$model->year?></p>
            </td>
        </tr>
    </table>
    <table class="table table-bordered mt-0 mb-0" style="margin-bottom: 0;">
        <tr>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>тех паспорт №</p>
                <p class="upper"><?=$model->insurer_tech_pass_series?> <?=$model->insurer_tech_pass_number?></p>
            </td>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Государственный номер</p>
                <p class="upper"><?=$model->autonumber?></p>
            </td>
            <td>
                <?php
                $price = $model->autocomp->price;
                $price = Kasko::getAutoRealPrice($price, $model->year);
                ?>
                <p class='text-bold' style='margin-bottom: 5px;'>Полная стоимость автомобиля</p>
                <p class="upper"><?=number_format(round($price, -3), 0,","," ")?> сум</p>
            </td>
        </tr>
    </table>
    <table class="table table-bordered mt-0 mb-0" style="margin-bottom: 0;">
        <tr style="border-top: none;">
            <th colspan=2>Условия страхования</th>
        </tr>
        <tr>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Период страхования</p>
                <p class="upper">с <?=date('d.m.Y', strtotime($model->begin_date)) . ' до ' .date('d.m.Y', strtotime("+364 day", strtotime($model->begin_date)));?></p>
            </td>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Общее страховое покрытие</p>
                <p class="upper"><?=number_format($model->price, 0,","," ")?> сум</p>
            </td>
            <td>
                <p class='text-bold' style='margin-bottom: 5px;'>Общая страховая премия</p>
                <p class="upper"><?=number_format($model->amount_uzs - $model->promo_amount, 0,","," ")?> сум</p>
            </td>
        </tr>
    </table>

    

        <div class="row" style="margin-top: 1rem;">
            <div style="float:left;width:73%;padding-left:15px;">
                <h5 style="font-size:12px;"><?=Yii::t('app', 'Генеральный директор ООО «GROSS INSURANCE» Назаров О.Х.')?></h5>
            </div>
            <div style="float:right;width:25%;text-align:right;">
                <div class="podpis"><img src="<?= Yii::getAlias('@frontend/web/img/pdf/lifepodpis.png') ?>" width="120px" /></div>
                <div class="pechat"><img src="<?= Yii::getAlias('@frontend/web/img/pdf/pechat.png') ?>" /></div>
            </div>
            <div class="clearfix"></div>
        </div>

    <div style="text-align: justify;">
        <h3 class="text-center" style="text-align: center;font-size: 14px;">Памятка клиенту</h3>
        <h4 style="font-size: 12px;">Попали в ДТП или произошло иное повреждение с автомобилем?</h4>
        
        <ol style="font-size: 12px; margin-left: 0; padding-left: 0;">
            <li>Заглушите двигатель. Вытащите ключ из замка зажигания и уберите его в карман. Включите стояночный тормоз и аварийную световую сигнализацию.</li>
            <li>Если есть пострадавшие, срочно вызовите скорую по телефонам 103. До ее приезда не оставляйте пострадавших.</li>
            <li>Поставьте знак аварийной остановки. Перед тем как выйти из автомобиля, наденьте светоотражающий жилет. В соответствии с ПДД знак аварийной остановки должен быть установлен на расстоянии 15 метров от вашей машины в населенном пункте и в 30 метрах вне населенного пункта.</li>
            <li>Не передвигайте автомобиль со своих мест.</li>
            <li>Вызовите аварийного комиссара по номеру 1166 или при помощи Мобильного приложения GROSS INSURANCE нажав на кнопку «SOS».  Наша группа аварийных комиссаров приедет в самые короткие сроки и избавит Вас от хлопот:</li>
        </ol>

        
        <div class="row">
            <div class="col-xs-10" style="font-size: 10px;" style="font-size: 11px;">
                <img src="<?= Yii::getAlias('@frontend/web/img/pdf/last.png') ?>" width="20px"/> Содействует оформлению документов
            </div>
            <div class="col-xs-10" style="font-size: 10px; margin-top: 5px;">
                <img src="<?= Yii::getAlias('@frontend/web/img/pdf/last.png') ?>" width="20px"/> Вызовет эвакуатора, будет взаимодействовать с участниками происшествия и правоохранительными органами
            </div>
            <div class="col-xs-10" style="font-size: 10px;margin-top: 5px;">
                <img src="<?= Yii::getAlias('@frontend/web/img/pdf/last.png') ?>" width="20px"/> Предпримет все необходимые меры исключительно в Ваших интересах
            </div>
        </div>
        <!-- <p class="text-bold" style="font-size: 9px;">В случае, если вышеупомянутые манипуляции не помогли, застрахованное ТС транспортируется до ближайшего автосервиса или ближайшей  охраняемой стоянки.</p> -->

        <div class="">
            <div style="float: right; width: 50%;">   
                <div>
                    <div style="width: 50%; float: left;">
                        <figure>
                            <div style="margin: 0 10px; text-align: center;"><?= str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 100, 'white', 'black')); ?></div>
                          <figcaption style="text-align: center;font-size:12px;">Правила страхования</figcaption>
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
    </div>

    

    <div style="margin-top: 5px;">
        <div style="width: 50%; float: left; font-size: 12px;">
            ЖЕЛАЕМ ВАМ СЧАСТЛИВОГО ПУТИ!
        </div>
        <div style="width: 50%; float: right; text-align: right; font-size: 12px;">
            Серия и номер полиса:
            <?=$model->policy_number?>
            <br>Дата выдачи: <?php

             echo date('d.m.Y', strtotime($model->trans->trans_date))?>
        </div>
        <div style="clear: both"></div>
    </div>

    
</div>