<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Partner extra insurance coefficients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Extra insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-form">
    <input type="hidden" id="eu" value="<?= $euro ?>">
    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-bordered" style=" table-layout: fixed;">
      <tr>
        <th rowspan="2">Partners/Extra insurances</th>
        <?php foreach($extra_insurances as $p):
                echo "<th colspan=3 style='text-align: center'>{$p->name_ru}</th>";
              endforeach;
        ?>
      </tr>
        <tr>
            <?php foreach($extra_insurances as $p): ?>
            <td class='col-sm-4'>Страховая сумма (euro)</td> <td class='col-sm-4'>Ставка (%)</td><td class='col-sm-4'>Цена в сумах</td>
            <?php endforeach; ?>
        </tr>
      <?php
      foreach ($partners as $i => $r) {
        echo "<tr><th>{$r->name}</th>";

        foreach($extra_insurances as $j => $p):

          foreach($models as $k => $pr) {
            if($pr->partner_id == $r->id && $pr->extra_insurance_id == $p->id) {
              $key = $k;
              break;
            }
          }

          echo Html::hiddenInput("TravelPartnerExtraInsurance[{$key}][id]", $models[$key]->id);
          echo Html::hiddenInput("TravelPartnerExtraInsurance[{$key}][partner_id]", $models[$key]->partner_id);
          echo Html::hiddenInput("TravelPartnerExtraInsurance[{$key}][extra_insurance_id]", $models[$key]->extra_insurance_id);
          echo "<td>". $form->field($models[$key], "[{$key}]sum_insured")->textInput(['onkeyup' => 'calc_baggage_amount('.$key.')'])->label(false) ."</td>";

          echo "<td>". $form->field($models[$key], "[{$key}]coeff")->textInput(['onkeyup' => 'calc_baggage_amount('.$key.')'])->label(false) ."</td>";
          echo "<td> <input value='". round($models[$key]->sum_insured * $euro * $models[$key]->coeff/100) ."' id='travelpartnerextrainsurance-$key-amount' type='text' class='form-control' readonly> </td>";

        endforeach;

        echo "<tr>";
      }

      ?>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function calc_baggage_amount(key){
        let eu = $(`#eu`).val();
        let sum_insured = $(`#travelpartnerextrainsurance-${key}-sum_insured`).val();
        let coeff = $(`#travelpartnerextrainsurance-${key}-coeff`).val();
        let amount = sum_insured*coeff*eu/100;
        $(`#travelpartnerextrainsurance-${key}-amount`).val(amount.toFixed(0));

        if (sum_insured)
            $(`#travelpartnerextrainsurance-${key}-coeff`).attr('required', 'required');
        else
            $(`#travelpartnerextrainsurance-${key}-coeff`).removeAttr('required');

        if (coeff)
            $(`#travelpartnerextrainsurance-${key}-sum_insured`).attr('required', 'required');
        else
            $(`#travelpartnerextrainsurance-${key}-sum_insured`).removeAttr('required');
    }
</script>