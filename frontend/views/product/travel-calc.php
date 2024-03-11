<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use common\models\Country;
use common\models\TravelPurpose;
use common\models\TravelGroupType;
use common\models\TravelExtraInsurance;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\MaskedInput;
use common\models\Travel;
use common\models\Page;

$page = Page::findOne(4);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'ru',
    'content' => $page->description_ru
]);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'uz',
    'content' => $page->description_uz
]);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'en',
    'content' => $page->description_en
]);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $page->keywords
]);


                // $order = Travel::find()->where(['trans_id' => 13])->one();
                // $text = 'Куплен полис TRAVEL ' . $order->partner->name . '. Номер телефона ' . $order->insurer_phone;

                //   Yii::$app->telegram->sendMessage(['chat_id' => 125708395,'parse_mode' => 'markdown','text' => $text]);
$this->title = Yii::t('app', 'Рассчитать стоимость Travel Insurance');
$this->registerJs("
  var CALC = false, SHENGEN = false;
  //$('.submit-box').hide();
  $('.res-box').hide();
  $('.field-travel-purpose_id').hide();
  $('.field-travel-isfamily').hide();
  $('.extra').hide();
  $('.travel-info').hide(); 
  $('.shengen').hide(); 

  function calcPolicy() {
    var countries = $('#travel-countries').val(),
        begin_date = $('#travel-begin_date').val(),
        end_date = $('#travel-end_date').val(),
        promo = $('#travel-promo_code').val(),
        purpose_id = $('input[name=\'Travel[purpose_id]\']:checked').val(),
        isFamily = $('#travel-isfamily:checked').val(),
        extraInsurances = [],
        travelers = [];

    $('input[name=\'Travel[travelExtraInsuranceBinds][]\']:checked').each(function() {
      extraInsurances.push($(this).val());
    });
    
    $('.travelers2').find('input').each(function() {
      let birthday = $(this).val();
      if(birthday) travelers.push(birthday);
    });

    if(countries.length > 0 && begin_date && end_date) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/get-travel-amounts') . "',
        data: {
          countries: countries,
          begin_date: begin_date,
          end_date: end_date,
          promo: promo,
          isFamily: nvl(isFamily),
          travelers: travelers,
          purpose: purpose_id,
          extraInsurances: extraInsurances,
        },
        dataType: 'json',
        success: function(result) {
          if(result) {
            $('.companies').html(result.html);
            $('.read-more').each(function(index) {              
            $(this).parent().parent().next().hide();
            $(this).removeClass('show-info');
            $(this).text('".Yii::t('app', 'Подробнее')."');
            });

        
          } else {
            $('.companies').html('');
          }

          if(!CALC) {
            $('.res-box').show();
            $('.field-travel-purpose_id').show();
            $('.field-travel-isfamily').show();
            $('.extra').show();
            $('.country-box').removeClass('col-md-6');
            $('.country-box').addClass('col-md-12');
            $('.dates-box').removeClass('col-md-6');
            $('.dates-box').addClass('col-md-12');
            $('.begin_date-box').removeClass('col-md-5');
            $('.begin_date-box').addClass('col-md-6');
            $('.end_date-box').removeClass('col-md-5');
            $('.end_date-box').addClass('col-md-6');
            $('.days-box').removeClass('col-md-2');
            $('.days-box').addClass('col-md-6');
            $('.calc-box').removeClass('col-lg-12');
            $('.calc-box').addClass('col-lg-4');
            $('.submit-box').hide();
            $('.item3').removeClass('col-lg-3');
            $('.item3').addClass('col-lg-12');
            $('.section-title').hide();
            $('.qc').hide();
            $('.box2').removeClass('col-md-10');
            $('.box2').removeClass('offset-md-1');
            $('.box2').addClass('col-md-12');
            $('.remove-item').css('left', '85%');
            $('.days').css('margin-top', '0');
            $('.n1').css({ 'background-color': '#fff' });
            $('.n2').css({ 'background-color': '#dfdfdf' });
            $('.promo').removeClass('col-md-4');
            $('.promo').addClass('col-md-12');
          }

          CALC = true;
          $('html, body').animate({ scrollTop: 0 },  1000);
        },
      });
    } else {
        $('.companies').html('');
    }

  }

  $('input[name=\'Travel[purpose_id]\']').change(function() {
    if($(this).val() != 3) {
      $('#travel-isfamily').attr('disabled', 'disabled');
      $('#travel-isfamily').prop('checked', false);
    } else {      
      $('#travel-isfamily').removeAttr('disabled');
    }
    
    checkISFamily();
    calcPolicy();
  });

  // $(document).ajaxStart(function(){
  //   $('.preloader').fadeIn();
  // });
  // $(document).ajaxComplete(function(){
  //   $('.preloader').fadeOut();
  // });

  $('.days').hide();

  function calcDays(i) {
    var begin_date = $('#travel-begin_date').kvDatepicker('getDate'),
        end_date = $('#travel-end_date').kvDatepicker('getDate');

    s_endDate = new Date(Date.parse(begin_date) + 364 * 24 * 60 * 60 * 1000);
    $('#travel-end_date').kvDatepicker('setStartDate', begin_date);
    $('#travel-end_date').kvDatepicker('setEndDate', s_endDate);

    var days = ((end_date - begin_date) / (1000 * 60 * 60 * 24)) + 1;
        days = days > 0 ? days : 0;

    if(begin_date && end_date) {
      $('.days').show();
      $('.travel-days').text(days);
    } else {
      $('.days').hide();
      $('.travel-days').text('');
    }    

    if(SHENGEN && end_date && days < 93) {
      fifteen_days_later = new Date().setDate(end_date.getDate()+15);
      endDate = new Date(Date.parse(end_date) + 15 * 24 * 60 * 60 * 1000);
      $('.shengen_end_date').text(moment(endDate).format('DD.MM.YYYY'));
    }
    if(CALC) calcPolicy();
  }

  var add = false;

  jQuery('.dynamicform_wrapper').on('afterInsert', function(e, item) {
    var cnt = $('.add-item').length;

    jQuery('.add-item').each(function(index) {
        if(index != cnt - 1) jQuery(this).hide();
        else jQuery(this).show();

        if(cnt == 6) jQuery(this).hide();
    });

    jQuery('.item3').each(function(index) {
        if(index == 2 && !add) {
          jQuery(this).after('<div class=\'col-lg-3\'></div>');
          add = true;
        }
        
    });

    if(CALC) {
      $('.item3').removeClass('col-lg-3');
      $('.item3').addClass('col-lg-12');
    }

  
  });

  jQuery('.dynamicform_wrapper').on('afterDelete', function(e) {
    jQuery('.travelers .dynamicform_wrapper .order_no').each(function(index) {
        jQuery(this).text(index + 1);
    });

    var cnt = $('.add-item').length;

    jQuery('.add-item').each(function(index) {
        if(index != cnt - 1) jQuery(this).hide();
        else jQuery(this).show();
    });
  });

  $('.field-travel-countries').addClass('required');

  function checkISFamily() {
    if ($('#travel-isfamily:checked').val() == 1) {
      $('.birthdays').slideUp();
    } else {
      $('.birthdays').slideDown();
    }
  }

  $('#travel-isfamily').change(function() {    
    checkISFamily();
    calcPolicy();
  });

  $('#travel-promo_code').change(function() {
    if(CALC) calcPolicy();
  });

  $('input[name=\'Travel[travelExtraInsuranceBinds][]\']').change(function() {
    calcPolicy();
  });

  $(document).on('click', '.read-more', function() {
    if($(this).hasClass('show-info')) {
      $(this).parent().parent().next().slideUp();
      $(this).removeClass('show-info');
      $(this).text('".Yii::t('app', 'Подробнее')."');
    } else {
      $(this).parent().parent().next().slideDown();
      $(this).addClass('show-info');
      $(this).text('".Yii::t('app', 'Скрыть')."');
    }
  });

  $('[data-toggle=\'popover\']').popover({ 
    html: true,
  });

  $('#calc-button').click(function() {
    $('#pur3').prop('checked', true);
    calcPolicy();
  });

  function checkShengen() {
    var countries = $('#travel-countries').val();

    if(countries.length > 0) {
      $.ajax({
        type: 'GET',
        url: '" . Yii::$app->urlManager->createUrl('product/check-shengen') . "',
        data: {
          countries: countries,
        },
        dataType: 'json',
        success: function(result) {
          if(result) {
            if(result.shengen) {
              $('.shengen').show();
              SHENGEN = true;
            } else {
              $('.shengen').hide();
              SHENGEN = false;
            }        
          }           
        },
      });
    }
  }

  function setCountry(country_id) {
    countries = $('#travel-countries').val();

    if(!countries) countries = [];

    countries.push(country_id);
    $('#travel-countries').val(countries).trigger('change');
    checkShengen();
    if(CALC) calcPolicy();
  }
", View::POS_END);

$form = ActiveForm::begin(['id' => 'travel-calc']);

?>

<div class="container">
  <div class="row number-cont">
    <div class="col-3">
      <div class="number-box n1" style="padding-left: 15px; background-color: #dfdfdf;">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>1</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Укажите детали поездки')?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="number-box n2">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>2</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Сравните предложения')?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="number-box">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>3</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Введите ваши данные')?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-3">
      <div class="number-box">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>4</h2>
          </div>
          <div class="number-text col-9">
            <p><?=Yii::t('app','Оплатите и получите полис')?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="container result-view">
  <section class="contact-area mt-lg-5 mt-0">
      <div class="row">
        <div class="col-lg-8 res-box">
          <div class="result">
            <div class="bg-white shadow p-2 border-bottom">
              <div class="row">
                <div class="col-md-3 col-6 p-2 pl-4">
                  <p class="text-center"><?=Yii::t('app','Company')?></p>
                </div>
                <div class="col-md-3 col-6 p-2">
                  <p class="text-center"><?=Yii::t('app','Insurance premium')?></p>
                </div>
                <div class="col-md-4">
                </div>
              </div>
            </div>
            <div class="companies">
              <?php 
              foreach($results as $i => $r) {
                echo "<div class='box bg-white shadow-sm p-2 border-bottom'>
                                  <div class='row'>
                                      <div class='col-md-3 p-2 pl-4'>
                                          <img src='/uploads/partners/".$r['partner']->image."' class='w-75' />
                                      </div>
                                      <div class='col-md-4 p-2 risks'>
                                        <p class='read-more pt-3'>".Yii::t('app', 'Подробнее')."</p>
                                      ";
                              
                              
                              echo "</div>
                                <div class='col-md-3 p-2'>
                                          <h5 class='pt-3'>".number_format($r['amount'],0,","," ") .' '. Yii::t('app', 'сум')."</h5>
                                      </div>
                                      <div class='col-md-2 pt-2'>
                                          ". Html::submitButton(Yii::t('app', 'Купить'), ['name' => 'Osago[partner_id]', 'value' => $r['partner']->id, 'class' => 'default-btn page-btn mini-btn'])."
                                      </div>
                                  </div>
                                  <div class='travel-info row'>
                                    <div class='nav flex-column nav-tabs col-4' id='v-pills-tab".$i."' role='tablist' aria-orientation='vertical'>
                      <a class='nav-link active' id='assistance-tab".$i."' data-toggle='tab' href='#assistance".$i."' role='tab' aria-controls='assistance".$i."' aria-selected='true'>".Yii::t('app','Assistance')."</a>
                      <a class='nav-link' id='franchise-tab".$i."' data-toggle='tab' href='#franchise".$i."' role='tab' aria-controls='franchise".$i."' aria-selected='true'>".Yii::t('app','franchise')."</a>
                      <a class='nav-link' id='limitation-tab".$i."' data-toggle='tab' href='#limitation".$i."' role='tab' aria-controls='limitation".$i."' aria-selected='true'>".Yii::t('app','limitation')."</a>
                      <a class='nav-link' id='rules-tab".$i."' data-toggle='tab' href='#rules".$i."' role='tab' aria-controls='rules".$i."' aria-selected='true'>".Yii::t('app','rules')."</a>
                      <a class='nav-link' id='policy_example-tab".$i."' data-toggle='tab' href='#policy_example".$i."' role='tab' aria-controls='policy_example".$i."' aria-selected='true'>".Yii::t('app','policy_example')."</a>
                    </div>
                    <div class='tab-content col-8' id='v-pills-tabContent'>
                      <div class='tab-pane fade show active' id='assistance".$i."' role='tabpanel' aria-labelledby='assistance-tab".$i."'>".$r['info']->assistance."</div>
                      <div class='tab-pane fade' id='franchise".$i."' role='tabpanel' aria-labelledby='franchise-tab".$i."'>".$r['info']->franchise."</div>
                      <div class='tab-pane fade' id='limitation".$i."' role='tabpanel' aria-labelledby='limitation-tab".$i."'>".$r['info']->limitation."</div>
                      <div class='tab-pane fade' id='rules".$i."' role='tabpanel' aria-labelledby='rules-tab".$i."'><a target='_blank' href='/uploads/travel_info/".$r['info']->rules."'>".Yii::t('app', 'Скачать правила страхования')."</a></div>
                      <div class='tab-pane fade' id='policy_example".$i."' role='tabpanel' aria-labelledby='policy_example-tab".$i."'><a target='_blank' href='/uploads/travel_info/".$r['info']->policy_example."'>".Yii::t('app', 'Образец полиса')."</a></div>
                    </div>
                                  </div>
                              </div>";
              }

              ?>
            </div>
          </div>
        </div>
        <div class="col-lg-12 calc-box">
          <div class="travel-form shadow bg-white p-4 mb-4">
            <div class="section-title">
              <h2><?= Yii::t('app', 'Укажите данные страхового полиса, чтобы узнать цену')?></h2>
            </div>

            <div class="row">
              <div class="col-md-10 offset-md-1 box2">
              <?php

              $url = \Yii::$app->urlManager->baseUrl . '/uploads/countries/';
              $format = <<< SCRIPT
              function format(state) {
                  if (!state.id) return state.text; // optgroup
                  src = '$url' +  state.id + '.png';
                  return '<span class="country-flag"><img src="' + src + '"/></span>' + state.text;
              }
              SCRIPT;
                    $escape = new JsExpression("function(m) { return m; }");
                    $this->registerJs($format, View::POS_HEAD);

              $countries = Country::find()->where(['not', ['parent_id' => null]])->orderBy('name_ru')->asArray()->all();
                    $data = [];
                    foreach($countries as $c) :
                      if(Yii::$app->language == 'ru') {
                        $data[$c['id']] = $c['name_ru'];
                      } elseif(Yii::$app->language == 'uz') {
                        if($c['name_uz']) $data[$c['id']] = $c['name_uz'];
                        else $data[$c['id']] = $c['name_ru'];
                      } else {
                        if($c['name_en']) $data[$c['id']] = $c['name_en'];
                        else $data[$c['id']] = $c['name_ru'];
                      }
                    endforeach;

                    ?>
                    <div class="row mt-4">
                      <div class="col-md-6 country-box">
                        <?php echo $form->field($model, 'countries')->widget(Select2::classname(), [
                          'data' => $data,
                          'theme' => Select2::THEME_BOOTSTRAP,
                          'options' => ['placeholder' => Yii::t('app','Select a state ...'), 'multiple' => true, 'autocomplete' => 'on', 'required' => 'required'],
                          'showToggleAll' => false,
                          'pluginOptions' => [
                              'templateResult' => new JsExpression('format'),
                              'templateSelection' => new JsExpression('format'),
                              'escapeMarkup' => $escape,
                              'allowClear' => true,
                              'closeOnSelect' => true
                          ],
                          'pluginEvents' => [
                              'select2:select' => 'function() { checkShengen(); if(CALC) calcPolicy(); }',
                              'select2:unselect' => 'function() { checkShengen(); if(CALC) calcPolicy(); }'
                          ],
                        ])->label(Yii::t("app","Выберите страну назначения/посещения"));
                        
                        $url = \Yii::$app->urlManager->baseUrl . '/uploads/countries/';
                        $quick_countries = Country::find()->where(['id' => [63, 140, 107, 109]])->all();

                        foreach($quick_countries as $qc) :
                          if(Yii::$app->language == 'ru') {
                            $name = $qc->name_ru;
                          } elseif(Yii::$app->language == 'uz') {
                            $name = $qc->name_uz;
                          } else {
                            $name = $qc->name_en;
                          }

                          echo  "<span style='cursor: pointer;' class='text-info qc' onclick='setCountry(\"".$qc->id."\")'><img width='20px' src='".$url.$qc->image."' /> ".$name."</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                        endforeach;

                        ?>
                      </div>
                        <div class="col-md-6 dates-box mt-lg-0 mt-3">
                          <div class="row">
                            <div class="col-md-5 begin_date-box">
                              <div class="form-group required">
                                <?php echo '<label class="control-label">'.Yii::t('app','Уезжаете').'</label>' . DatePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'begin_date',
                                    'name' => 'date',
                                    'type' => DatePicker::TYPE_INPUT,
                                    'pluginOptions' => [
                                'todayHighlight' => true,
                                        'autoclose' => true,
                                        'autoApply' => true,
                                        'format' => 'dd.mm.yyyy',
                                        'startDate' => '+1d',
                                        'endDate' => '+2y'
                                    ],

                                    'options' => [
                                      'required' => true
                                    ],
                                    'pluginEvents' => [
                                     'changeDate' => 'function() {
                                      $("#travel-end_date").kvDatepicker("show");
                                        calcDays(1);
                                     }',
                                    ],
                                ]);

                                ?>
                                
                            </div>
                            </div>
                            <div class="col-md-5 end_date-box">
                              <div class="form-group required">
                                <?php echo '<label class="control-label">'.Yii::t('app', 'Возвращаетесь').'</label>' . DatePicker::widget([
                                    'model' => $model,
                                    'attribute' => 'end_date',
                                    'name' => 'date',
                                    'type' => DatePicker::TYPE_INPUT,
                                   // 'value' => date('d.m.Y'),,
                                    'pluginOptions' => [
                                'todayHighlight' => true,
                                        'autoclose' => true,
                                        'format' => 'dd.mm.yyyy',
                                    ],

                                    'options' => [
                                      'required' => true
                                    ],
                                    'pluginEvents' => [
                                     'changeDate' => 'function() {
                                        calcDays(1);
                                     }',
                                    ],
                                ]);
                                ?>
                            

                           
                            </div>
                            </div>
                            <div class="col-md-2 days-box">
                              <p class="days" style="margin-top: 30px;"><?=Yii::t('app', 'Select {days} days', ['days' => '<span class="travel-days"></span>'])?></p>
                            </div>
                            <div class="col-md-6">
                              <p class="shengen">
                              <span>+15 <?=Yii::t('app', 'days')?>&nbsp;
                                <sup><?='<span class="shengen-info badge badge-pill badge-primary" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="'.Yii::t('app', 'При оформлении Полиса в страны Шенгенского соглашения до 92-х дней, к периоду страхования добавляются дополнительные 15 дней, которые при определении расчетной стоимости Полиса не учитываются.').'">?</span>'?></sup>
                              </span>
                              <span class="w-50 shengen_end_date border-bottom border-primary">

                              </span>
                            </p>
                            </div>
                          </div>
                          
                    </div>
                    </div>

                      <?php
                      $purposes = TravelPurpose::find()->asArray()->all();

                    if(Yii::$app->language == 'ru') {
                      $items = ArrayHelper::map($purposes, 'id', 'name_ru');
                    } elseif(Yii::$app->language == 'uz') {
                      $items = ArrayHelper::map($purposes, 'id', 'name_uz');
                    } else {
                      $items = ArrayHelper::map($purposes, 'id', 'name_en');
                    }


                      echo $form->field($model, 'purpose_id')->label(Yii::t('app','Укажите цель поездки'))->radioList($items,
                        [
                          'item' => function($index,$label,$name,$checked,$value){
                            $check = $checked ? ' checked="checked"' : '';
                            return '<div class="custom-control custom-radio my-1 mr-sm-2">
                              <input name="'.$name.'" value="'.$value.'"'.$check.' type="radio" class="custom-control-input" id="pur'.$value.'">
                              <label class="custom-control-label" for="pur'.$value.'">'. $label.'</label>
                            </div>';
                          }
                        ]);

                        
                          echo $form->field($model, 'isFamily', ['template' =>
                        "<div class='custom-control custom-checkbox my-1 mr-sm-2 mt-3'>
                                <input type='checkbox' class='custom-control-input' id='travel-isfamily' name='Travel[isFamily]' value='1'><label class='custom-control-label' for='travel-isfamily'>".Yii::t('app', 'Путешествуем с семьей') ."</label>
                              </div>"])->checkbox();
                      ?>

                <div class="birthdays mt-4">
                        <label class="control-label"><?=Yii::t('app','Укажите дату рождения участников поездки')?></label>
                  <div class="travelers2">
                            <?php DynamicFormWidget::begin([
                              'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                              'widgetBody' => '.container-items3', // required: css class selector
                              'widgetItem' => '.item3', // required: css class
                              'limit' => 6, // the maximum times, an element can be cloned (default 999)
                              'min' => 1, // 0 or 1 (default 1)
                              'insertButton' => '.add-item', // css class
                              'deleteButton' => '.remove-item', // css class
                              'model' => $travelers[0],
                              'id' => 'dynamic-form',
                              'formId' => 'travel-calc',
                              'formFields' => [
                                  'birthday',
                              ],
                            ]); ?>

                          <div class=""><!-- widgetContainer -->
                            <div class="row container-items3">
                          <?php foreach ($travelers as $i => $traveler): ?>
                              <div class="col-lg-3 item3">
                                <div class="row">
                                  <div class="col-10">
                                <?php echo $form->field($traveler, "[{$i}]birthday")->widget(MaskedInput::className(), [
                                  // 'mask' => "1.2.y",
                                  'options' => [
                                    "class" => "form-control birthday",
                                    "onblur" => "
                                      if(CALC) calcPolicy();
                                    "
                                  ],
                            'clientOptions' => [
                                'alias' => 'dd.mm.yyyy',
                                "placeholder" => Yii::t('app', "dd.mm.yyyy"),
                            ]
                              ])->label(false);
                              ?>
                              <span class="remove-item"><i class="fa fa-remove"></i></span>
                          </div>
                          <div class="col-2">
                              <span class="add-item"><i class="fa fa-user-plus"></i></span>
                          </div>
                          </div>
                              </div>
                          <?php endforeach; ?>
                            </div>
                          </div>
                          <?php DynamicFormWidget::end(); ?>
                        </div>
                </div>

                <div class="mt-4 extra">

                <?php
                $extra_insurances = TravelExtraInsurance::find()->asArray()->all();
            
                      if(Yii::$app->language == 'ru') {
                        $items = ArrayHelper::map($extra_insurances, 'id', 'name_ru');
                      } elseif(Yii::$app->language == 'uz') {
                        $items = ArrayHelper::map($extra_insurances, 'id', 'name_uz');
                      } else {
                        $items = ArrayHelper::map($extra_insurances, 'id', 'name_en');
                      }

                echo $form->field($model, 'travelExtraInsuranceBinds')->label(Yii::t('app', 'Дополнительные'))->checkboxList($items,
                          [
                            'item' => function($index,$label,$name,$checked,$value){
                              $check = $checked ? ' checked="checked"' : '';
                              return '<div class="custom-control custom-checkbox my-1 mr-sm-2">
                                <input name="'.$name.'" value="'.$value.'"'.$check.' type="checkbox" class="custom-control-input" id="extra'.$value.'">
                                <label class="custom-control-label" for="extra'.$value.'">'. $label.'</label>
                              </div>';
                            }
                          ]) ?>
                      </div>

                <div class="row">
                  <div class="col-md-4 promo">
                  <?php 

                  echo $form->field($model, 'promo_code')->textInput()->label(Yii::t('app','Promo code'));

                  ?>
                  </div>
                </div>

                <div class="row mt-4 submit-box">
                  <div class="col-lg-12 col-md-12 text-center">
                    <button id="calc-button" type="button" class="mybtn page-btn">
                      <?= Yii::t('app', 'Рассчитать стоимость')?>
                    </button>
                  </div>
                </div>

                  </div>
                </div>
        </div>
      </div>
  </section>
</div>
<?php

?>


<?php

ActiveForm::end(); 
?>
