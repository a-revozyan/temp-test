<?php
use common\models\Travel;
use common\models\Traveler;
use common\models\TravelCountry;


$travel = Travel::findOne([$travel_id]);
$travelers = Traveler::find()->where(['travel_id' => $travel_id])->all();
$countries = (new \yii\db\Query())
            ->select(['country.id', 'country.name_en'])
            ->from('travel_country')
            ->join('INNER JOIN', 'country', 'country.id=travel_country.country_id')
            ->where(['travel_country.travel_id' => $travel_id])
            ->all();
?>
<div class="container-fluid pdf">
    <div class="row" style="margin: 0;padding:0 -15px;">
      <div class="header" style="float: left; width: 75%;padding-left: 15px;">
        <h2 style="font-size: 18px;">Полис страхования путешествующих</h2>
        <h2 style="font-size: 18px;">International Travel Insurance policy</h2>
      </div>
      <div style="text-align: right;float: left; width: 22%;">
        <img class="img img-responsive" src="/img/pdf/logo.png" />
      </div>
      <div style="clear: both"></div>
    </div>
    <table class="table table-bordered mt-4 mb-0" style="margin: 0 -15px;margin-top: 10px;">
        <tr>
            <th colspan=2>Данные страхователя / Data of insurer</th>
        </tr>
        <tr>
            <td>
                <p class="text-bold m-0">Страхователь / Insurer</p>
                <p class="upper m-0"><?=$travel->insurer_name?></p>
            </td>
            <td>
                <p class="text-bold m-0">Контактный номер / Contact number</p>
                <p class="upper m-0"><?=$travel->insurer_phone?></p>
            </td>
        </tr>
    </table>
    <table class="table table-bordered mt-0 mb-0" style="margin: 0 -15px;">
        <tr>
            <th colspan=3>Данные застрахованных лиц / Data of insured persons</th>
        </tr>
        <?php foreach($travelers as $index => $tr): ?>
        <tr>
            <td>
                <?php if($index == 0) echo "<p class='text-bold m-0'>Застрахованные лица / Insured persons</p>"; ?>
                <p class="upper m-0"><?=($index + 1) . '.' . $tr->name?></p>
            </td>
            <td>
                <?php if($index == 0) echo "<p class='text-bold m-0'>Дата рождения / Date of birth</p>";?>
                <p class="upper m-0"><?=date('d.m.Y', strtotime($tr->birthday))?></p>
            </td>
            <td>
                <?php if($index == 0) echo "<p class='text-bold m-0'>Детали паспорта / Passport details</p>";?>
                <p class="upper m-0"><?=$tr->passport_series . ' ' . $tr->passport_number?></p>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
    <table class="table table-bordered" style="margin: 0 -15px;">
        <tr>
            <th colspan=3>Условия страхования / Insurance terms</th>
        </tr>
        <tr>
            <td>
                <p class="text-bold m-0">Территория страхования / Territory of cover</p>
                <p class="upper m-0"><?php echo implode(', ', array_column($countries, 'name_en')); ?></p>
            </td>
            <td>
                <p class="text-bold m-0">Период страхования / Insurance period</p>
                <p class="upper m-0">c <?=date('d.m.Y', strtotime($travel->begin_date))?> до <?=date('d.m.Y', strtotime($travel->end_date))?></p>
            </td>
            <td>
                <p class="text-bold m-0">Дней / Days</p>
                <p class="upper m-0"><?=$travel->days?></p>
            </td>
        </tr>

        <tr>
            <?php
            $sum = 0;
            $risks = [];

            foreach($travel->program->travelProgramRisks as $pr_risk):
                if($pr_risk->risk_id != 7) :
                    $sum += $pr_risk->amount;
                    $n = [
                        'name' => $pr_risk->risk->name_ru . ' / ' . $pr_risk->risk->name_en,
                        'amount' => number_format($pr_risk->amount,0,","," "),

                    ];
                    $risks[] = $n;
                endif;
            endforeach;

//            usort($risks, function ($item1, $item2) {
//                return $item1['order_no'] <=> $item2['order_no'];
//            });

            foreach($risks as $pr_risk):
            ?>
            <td>
                <p class="text-bold m-0"><?=$pr_risk['name']?></p>
                <p class="upper m-0"><?=$pr_risk['amount']?> EUR</p>
            </td>
            <?php
            endforeach;
            ?>
        </tr>
    </table>
    <table class="table table-bordered mt-0 mb-0" style="margin: 0 -15px;">        
        <tr>
            <td>
                <p class="text-bold m-0">Общее страховое покрытие / Total sum insured</p>
                <p class="upper m-0"><?=number_format($sum,0,","," ")?> EUR</p>
            </td>
            <td>
                <p class="text-bold m-0">Программа / Program</p>
                <p class="upper m-0"><?=$travel->program->name?></p>
            </td>
            <td>
                <p class="text-bold m-0">Особые условия / Special conditions</p>
                <p class="upper m-0"><?php if($travel->program_id == 6 || $travel->program_id == 7) echo "COVID - 19";
                echo '/' . $travel->purpose->name_en; ?></p>
            </td>
            <td>
                <p class="text-bold m-0">Страховая премия / Insurance premium</p>
                <p class="upper m-0"><?=number_format($travel->amount_uzs,0,","," ")?> UZS</p>
            </td>
        </tr>
    </table>

    

    <div class="row" style="margin-top: 1rem;">
        <div style="float:left;width:73%;padding-left:15px;">
            <h5 style="font-size:12px;"><?=Yii::t('app', 'Генеральный директор ООО «GROSS INSURANCE» Назаров О.Х.')?></h5>
        </div>
        <div style="float:right;width:25%;text-align:right;">
            <div class="podpis"><img src="/img/pdf/lifepodpis.png" width="120px" /></div>
            <div class="pechat"><img src="/img/pdf/pechat.png" /></div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row assistance">
        <p style="font-size: 12px;">Чтобы получить необходимую медицинскую помощь в путешествии, при внезапном заболевании и/или несчастном случае, обращайтесь в круглосуточные сервисные центры Службы Ассистанса следующими способами</p>
        <div style="float:left;width:48%;">
            <div>
                <div style="float:left;width:24%; margin-top: 40px;">
                    <p style="font-size: 12px;"><span><img width="20px" src="/uploads/countries/109.png" /></span> Турция</p>
                </div>
                
               
                <div class="clearfix"></div>
            </div>
            
        </div>
      <div style="float:left;width:17%;padding-top:40px;padding-left:10px;">
            <img src="/img/pdf/RemedAssistanceVertical.png" class="img img-responsive" />
        </div>
        <div class="contacts" style="float:right;width:34%; padding-right:-55px;">
            <div>
                <div style="">
                    <p class="" style="font-size:10px;text-align: left;"><img width="20px" src="/img/pdf/phone.png" />&nbsp;+90 (242) 310 28 43</p>
                    <p class="mt-3" style="font-size:10px;text-align: left;"><img width="25px" src="/img/pdf/whatsapp.png" />+41 78 856 07 42</p>
                    <p class="mt-3" style="font-size:10px;text-align: left;"><img width="25px" src="/img/pdf/telegram.png" />+90 (531) 250 45 96</p>
                      <p class="" style="font-size:10px;text-align: left;"><img width="20px" src="/img/pdf/email.png" /> gross@remed.com.tr</p>
               </div>
                <div class="clearfix"></div>
            </div>

        </div>
        
    </div>





    <div class="row assistance" style="margin-top:5px;">
        <div style="float:left;width:48%;">
            <div>
                
                <div style="float:left;width:60%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/63.png" class="img img-responsive" /></span> Объединённые Арабские Эмираты</p>
                </div>
                <div style="float:left;width:24%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/99.png" /></span> Таиланд</p>
                </div>
            </div>            
            <div>
                <div style="float:left;width:24%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/112.png" class="img img-responsive" /></span> Иордания</p>
                </div>
                <div style="float:left;width:24%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img 
                       width="20px" src="/uploads/countries/150.png" class="img img-responsive" /></span> Марокко</p>
                </div>
                <div style="float:left;width:25%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/124.png" class="img img-responsive" /></span> Камбоджа</p>
                </div>
                <div style="float:left;width:25%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/111.png" class="img img-responsive" /></span> Индонезия</p>
                </div>
                <div style="float:left;width:24%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/91.png" class="img img-responsive" /></span> Сингапур</p>
                </div><div style="float:left;width:24%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img 
                       width="20px" src="/uploads/countries/84.png" class="img img-responsive" /></span> Вьетнам</p>
                </div>
                <div style="float:left;width:25%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/147.png" /></span> Малайзия</p>
                </div>
                <div style="float:left;width:25%;">
                    <p style="font-size: 9px;"><span class="round-flag"><img width="20px" src="/uploads/countries/115.png" /></span> Филиппины</p>
                </div>
                
                <div class="clearfix"></div>
            </div>
        </div>
        <div style="float:left;width:17%;padding-top:40px;padding-left:10px;">
            <img src="/images/pdf/RemedAssistanceVertical.png" class="img img-responsive" />
        </div>
        <div class="contacts" style="float:right;width:34%; padding-right: -55px;">
            <div>
                     <div style="">
                    <p class="m-0" style="font-size:10px;text-align: left;"><img width="20px" src="/img/pdf/phone.png" />&nbsp;+90 (242) 310 28 44</p>
                    <p class="mt-3 m-0" style="font-size:10px;text-align: left;"><img width="25px" src="/img/pdf/whatsapp.png" />+41 78 856 07 42</p>
                    <p class="mt-3" style="font-size:10px;text-align: left;"><img width="25px" src="/img/pdf/telegram.png" />+90 (531) 250 45 96</p>
                      <p class="m-0" style="font-size:10px;text-align: left;"><img width="20px" src="/img/pdf/email.png" /> gross@remed.com.tr</p>
               </div>
                <div class="clearfix"></div>
            </div>

        </div>
        <div class="clearfix"></div>
    </div>
<div class="row assistance">
        <div style="float:left;width:48%;">
            <div>
                <br>
                <br>
                <div style="float:left;width:24%;">
                    <p style="font-size: 12px;"><span><img width="20px" src="/uploads/countries/107.png" /></span> Египет</p>
                </div>
                <div style="float:left;width:25%;">
                    <p style="font-size: 12px;"><span class="round-flag"><img width="20px" src="/uploads/countries/103.png" class="img img-responsive" /></span> Тунис</p>
                </div>
                
               
                <div class="clearfix"></div>
            </div>
        </div>
      <div style="float:left;width:17%;padding-top:40px;padding-left:10px;">
            <img src="/images/pdf/RemedAssistanceVertical.png" class="img img-responsive" />
        </div>
        <div class="contacts" style="float:right;width:34%; padding-right: -55px;">
            <div> 
                  <div style="">
                    <p class="m-0" style="font-size:10px;text-align: left;"><img width="20px" src="/img/pdf/phone.png" />&nbsp;+2 02 24137308</p>
                    <p class="mt-3" style="font-size:10px;text-align: left;"><img width="25px" src="/img/pdf/whatsapp.png" />+201023543838</p>
                    <p class="mt-3" style="font-size:10px;text-align: left;"><img width="25px" src="/img/pdf/telegram.png" />+201066363444</p>
                     <p class="m-0" style="font-size:10px;text-align: left;"><img width="20px" src="/img/pdf/email.png" /> gross@remed.com.tr</p>
               </div>
                <div class="clearfix"></div>
            </div>

        </div>
        <div class="clearfix"></div>
    </div>


    <div class="row" style="margin-top: 8px;">
        <div style="float:left;width:33%;">
            <h5>В любых других странах</h5>
        </div>
        <div style="float:left;width:23%;">
            <img src="/img/pdf/allianz_assistance_logo.png" class="img img-responsive" />
        </div>
        <div class="contacts" style="float:left;width:43%;">
            <div>
                <div style="float:left;width:49%;">
                    <p style="font-size:10px;text-align: center;" class="m-0"><span class="c-icon"><i class="fa fa-telegram"></i></span><img width="20px" src="/img/pdf/phone.png" /> +7 (495) 212 21 43</p>
                </div>

                <div style="float:left;width:49%;">
                    <p style="font-size:10px;text-align: right; color: red;" class="m-0"><span class="c-icon"><i class="fa fa-envelope"></i></span><img width="20px" src="/img/pdf/email.png" /> assistance.ru@allianz.com</p>
                </div>
                <div class="clearfix"></div>
            </div>

        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row assistance" style="margin-bottom: 6px;">
        <div style="float:left;width:98%;">
            <p style="font-size:10px;text-align: right;text-decoration: underline; color: red;" class="m-0">Бесплатный интернет звонок: www.allianz-worldwide-partners.ru</p>
        </div>
        <div class="clearfix"></div>
    </div>
   <div class="row assistance pt-3 pb-3">
     <div style="float:left;width:59%; margin-left: 3px;">
            <p style="font-weight: normal; font-size: 10px;">Если у вас нет возможности позвонить в службу Ассистанс, Вы можете мгновенно связаться с
нашими операторами при помощи мобильного приложения “GROSS MOBILE”. Достаточно
нажать на Кнопку <span class="c-icon"><i > <img width="22px" src="/img/pdf/sos.png"></i></span> и затем указать Ваш контактный номер, чтобы операторы нашего
круглосуточного Центра поддержки клиентов смогли установить Ваше текущее
местоположение и перезвонить Вам. При отсутствии телефонного номера в стране пребывания,
Вы сможете выйти на связь при помощи популярных мессенджеров Telegram, Whatsapp и Viber</p>
     </div>

    <div style="float:left;width:39%; margin-left: 3px;">
        <div>
                <div style="width: 50%; float: left;">
                    <figure>
                        <div style="margin: 0 10px; text-align: center;"><img style="width: 70%;" src="/public_offers/qr-codes/qr-code-mobile.png" /></div>
                      <figcaption style="text-align: center;font-size:12px;">Мобильное приложение</figcaption>
                    </figure>
                </div>
                <div style="width: 50%; float: right;">   
                    <figure>
                        <div style="margin: 0 10px; text-align: center;"><img style="width: 70%;" src="/public_offers/qr-codes/travel_ru.png" /></div>
                      <figcaption style="text-align: center;font-size:12px;">Правила страхования</figcaption>
                    </figure>
                </div>
            </div>
    </div>
    <div class="clearfix"></div>
   </div>
   <div class="row" style="margin-top: 4px;">
        <div style="width: 60%; float: left; font-size: 14px;text-transform: uppercase;">
            Желаем вам приятного путешествия
        </div>
        <div style="width: 40%; font-size:14px; float: right; text-align: right;">
            Серия и номер полиса: <?=$travel->policy_number?><br>Дата выдачи: <?php
            echo date('d.m.Y', strtotime($travel->trans->trans_date));
            ?>
        </div>
        <div style="clear: both"></div>
    </div>
</div>