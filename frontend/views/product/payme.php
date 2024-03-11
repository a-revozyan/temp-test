<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap4\Accordion;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Оплата онлайн');
$this->registerJs("
  function checkMethod() {
    var method = parseFloat($('input[name=\'PaymeForm[method]\']:checked').val());
    
    if(method == 0) {
      $('.with-reg').hide();
      $('.from-payme').hide();
      $('.without-reg').show();
      $('input[name=\'PaymentForm[phone]\']').val('');
    } else if(method == 1) {
      $('.with-reg').show();
      $('.from-payme').hide();
      $('.without-reg').hide();
      $('input[name=\'PaymentForm[number]\']').val('');
      $('input[name=\'PaymentForm[expiry]\']').val('');
    } else if(method == 2) {
      $('.with-reg').hide();
      $('.from-payme').show();
      $('.without-reg').hide();
      $('input[name=\'PaymentForm[phone]\']').val('');
      $('input[name=\'PaymentForm[number]\']').val('');
      $('input[name=\'PaymentForm[expiry]\']').val('');
    }
  }

  checkMethod();
  
  $('#polis-price').text(formatNumber(parseFloat($('#polis-price').text()), 2));

", \yii\web\View::POS_END);
?>
<div class="container">
  <div class="row number-cont">
    <div class="col-3">
      <div class="number-box n1" style="padding-left: 15px;">
        <div class="row n-rows">
          <div class="numbers col-3">
            <h2>1</h2>
          </div>
          <div class="number-text col-9">
            <p><?php
            if($product == 'kasko' || $product == 'osago')
              echo Yii::t('app','Выберите Ваше авто');
            elseif($product == 'travel')
              echo Yii::t('app','Укажите детали поездки');
            else
              echo Yii::t('app','Выберите');
            ?></p>
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
      <div class="number-box" style="background-color: #dfdfdf;">
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

<div class="container mt-5 mb-4">
  
  <div class="container">

 <div class="">
      <div class="text-center"><img width="150px" src="/img/payme.png"></div>
      <?php $form = ActiveForm::begin(); 

      $items = [
        0 => Yii::t('app', 'without registration on payme'), 
        1 => Yii::t('app', 'using registered phone number'), 
        2 => Yii::t('app', 'from payme.uz')
      ];
      
      if(!$sent && !$verify):
        ?>

        <div class="row">
          <div class="col-md-6 offset-md-3 mt-3">

        <?php
        echo $form->field($payme, 'method')->label(Yii::t('app', 'Select method of paying'))->radioList($items,
            [
              'item' => function($index,$label,$name,$checked,$value){
                $check = $index == 0 ? ' checked="checked"' : '';
                return '<div class="custom-control custom-radio my-1 mr-sm-2 mt-3">
                  <input name="'.$name.'" value="'.$value.'"'.$check.' type="radio" class="custom-control-input" id="meth'.$value.'">
                  <label class="custom-control-label" for="meth'.$value.'">'. $label.'</label>
                </div>';
              },
              'onchange' => 'checkMethod()'
            ]);
        ?>

          </div>
        </div>

        <?php
      endif;

      if($verify) :
        echo $form->field($payme, 'verifyCode')->textInput(['maxlength' => 6])->label(Yii::t('app', 'Please, enter the verification code which sent to {phone} while {time} seconds', [
          "phone" => $verifyPhone, "time" => $verifyWait/1000
        ]));
        echo Html::hiddenInput('verify', 1);
        echo $form->field($payme, 'number')->hiddenInput(['value' => $payme->number])->label(false);
        echo $form->field($payme, 'expiry')->hiddenInput(['value' => $payme->expiry])->label(false);
        echo Html::hiddenInput('token', $token);
        ?>
        <div class="row mt-3">
          <div class="col-md-12 text-center">
          <?= Html::submitButton(Yii::t('app', 'Оформить онлайн'), ['class' => 'mybtn', 'id' => 'button'])?>
          </div>
        </div>
        <?php
        else :
          if($sent):
        ?>

        <div class="row mt-4">
          <div class="col-md-6 offset-md-3">
            <p class="text-center"><?=Yii::t('app', 'Чек на оплату отправлено в SMS-сообщении на номер {phone}', ['phone' => $phone])?></p>

            <p class="mt-3 text-center">
              <?=Yii::t('app', 'После оплаты, нажмите на кнопку "UPDATE"')?>
            </p>
            <p class="mt-2 text-center">
              <?=Html::a(Yii::t('app', "UPDATE"),[$url, 'id' => $id], ['class' => 'mybtn'])?>
            </p>
          </div>
        </div>
        <?php 
        else :
        ?>
        <div class="row mt-4">
          <div class="col-md-6 offset-md-3 without-reg">
            <div class="card">
              <div class="card-header"><?Yii::t('app', '')?></div>
              <div class="card-body">
                <?php
                echo $form->field($payme, "number")->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '9999 9999 9999 9999',
                ])->label(Yii::t('app','Номер карты'));
                ?>
                <div class="row mt-3">
                  <div class="col-md-5 offset-md-7">
                  <?= $form->field($payme, 'expiry')->widget(\yii\widgets\MaskedInput::className(), [
                  'mask' => '99/99',
              ])->label(Yii::t('app','Expiry')) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 offset-md-3 with-reg">
            <div class="card h-100">
              <div class="card-header"><?Yii::t('app', '')?></div>
              <div class="card-body">
                <?php
                echo $form->field($payme, "phone")->textInput(['placeholder' => '998XXXXXXXXX'])->label(Yii::t('app','Номер телефона'));
                ?>
              </div>
            </div>
          </div>
          <div class="col-md-6 offset-md-3 from-payme">
            <div class="card h-100">
              <div class="card-header"><?Yii::t('app', '')?></div>
              <div class="card-body">
                
                <a class="btn btn-link" href='https://www.payme.uz' target="_blank"><?=Yii::t('app', 'Go to www.payme.uz')?></a>
              </div>
            </div>
          </div>
        </div>        

        <div class="row">
          <div class="col-md-6 offset-md-3 mt-3 text-center">
          <?= Html::submitButton(Yii::t('app', 'Оформить онлайн'), ['class' => 'mybtn', 'id' => 'button'])?>
          </div>
        </div>
        <?php 
        endif;
      endif;?>

        <?php ActiveForm::end(); ?>

</div>
</div>
</div>
