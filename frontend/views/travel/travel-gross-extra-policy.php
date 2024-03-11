<?php

use common\helpers\GeneralHelper;use common\models\Currency;
use common\models\Kasko;
use common\models\TravelPartnerExtraInsurance;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$model = \common\models\Travel::findOne([$id]);

$qrCode = new QrCode(GeneralHelper::env('frontend_project_website').'/travel/travel-extra-policy');
$output = new Output\Svg();

?>
<div class=" pdf" >
    <div class="row">
        <div class="col-md-3">
            <img class="img img-responsive w-25" src="/uploads/partners/<?= $model->partner->image ?>" />
        </div>
        <div class="col-md-9">
            Полис страхования непредвиденных расходов лиц, выезжающих
            за пределы Республики Узбекистан
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            Полис номер: <?= $model->policy_number ?> <br>
            Дата выдачи: <?= date('Y-m-d', $model->payed_date) ?>
            <br>
            <br>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            Настоящий Полис подтверждает заключение с Вами Договора страхования в соответствии с принятой Вами
            Публичной оферты и утвержденных в ООО «GROSS INSURANCE» Правил страхования непредвиденных расходов
            пассажиров, выезжающих за пределами Республики Узбекистан (далее по тексту «Правила страхования»).
            <br>
            <br>
        </div>
    </div>
    <div>
        Страхователь/Policyholder: <?= $model->insurer_name ?> <br>
        Адрес, телефон/ Address phone number: <?= $model->insurer_address ?>, <?= $model->insurer_phone ?>
        <br>
    </div>
    <div class="row">
        <div class="col-md-8">
            <table class="small">
                <tr>
                    <td>
                        Застрахованный (ФИО)/
                        Insured (Full name)
                    </td>
                    <td>
                        Дата
                        рождения/Date
                    </td>
                    <td>
                        Паспортные
                        данные/ Passport
                    </td>
                </tr>
                <tr>
                    <td><?= $model->insurer_name ?></td>
                    <td><?= $model->insurer_birthday ?></td>
                    <td><?= $model->insurer_passport_number ?></td>
                </tr>
                <?php foreach ($model->travelMembers as $travelMember)  {?>
                <tr>
                    <td><?= $travelMember->name ?></td>
                    <td><?= $travelMember->birthday ?></td>
                    <td><?= $travelMember->passport_series ?> <?= $travelMember->passport_number ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div class="col-md-4 small">
            Период страхования/ Insurance period: <br>
            <?= $model->begin_date ?> – <?= $model->end_date ?> <br>
            Период возврата/ Refund period
            <?php
                if (abs((strtotime($model->begin_date) - $model->payed_date) / (60 * 60 * 24)) >= 3)
                    echo date('Y-m-d', $model->payed_date) . " - " . $model->begin_date;
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            Программа страхования: <?= $model->program->name?>
            <table class="bordered">
                <tr>
                    <td>
                        №
                    </td>
                    <td>
                        Страховые риски в соответствии с
                        программой страхования
                    </td>
                    <td>
                        Страховая сумма на каждого
                        застрахованного
                    </td>
                    <td>
                        Страховая
                        премия
                    </td>
                </tr>
                <?php
                    $counter = 0;
                    $eu = Currency::getEuroRate();
                    foreach ($model->travelExtraInsurances as $extraInsurance) {
                        $counter++;
                        $extraInsAmount = TravelPartnerExtraInsurance::find()
                            ->where(['extra_insurance_id' => $extraInsurance->id, 'partner_id' => $model->partner->id])
                            ->one();
                ?>
                    <tr>
                        <td>
                            <?= $counter ?>
                        </td>
                        <td>
                            <?= $extraInsurance->name_ru ?>
                        </td>
                        <td>
                            <?= $extraInsAmount->sum_insured ?> uero <br>
                            <?= $extraInsAmount->coeff ?> % <br>
                            <?= round($extraInsAmount->coeff * $eu * $extraInsAmount->sum_insured / 100, -3) ?> sum
                        </td>
                        <td>
                            <?= round($extraInsAmount->coeff * $eu * $extraInsAmount->sum_insured / 100, -3) ?> сум
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <table style="width: 100%; text-align: center">
        <tr>
            <td>
                Генеральный директор <br>
                ООО «GROSS INSURANCE»
            </td>
            <td>О.Х. Назаров</td>
            <td>
                <?= str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 100, 'white', 'black')); ?>
                <br>
                Правила страхования <br>
                Услуги лицензированы
            </td>
        </tr>
    </table>
    <div class="row">
        <div class="col-md-12">
            ВНИМАНИЕ <br>
            При возникновении страхового случая, прежде чем предпринять какие-либо действия, Вам необходимо:
            <ol style="margin: 0;">
                <li  style="margin: 0;">
                    Связаться с Ассистанс компанией по телефонам:
                    Для звонков из любой точки мира - +7 (495) 212-21-43, Турция +90 (242) 310-28-43, Объединённые Арабские
                    Эмираты +90 (242) 310-28-44;
                </li>
                <li  style="margin: 0;">
                    Сообщите Ассистанс компании свои данные и возникшую проблему.
                </li>
            </ol>
        </div>
    </div>

</div>