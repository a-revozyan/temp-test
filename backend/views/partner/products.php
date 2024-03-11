<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Country;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Partner product percents');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-bordered">
      <tr>
        <th>Partners/Products</th>
        <?php foreach($products as $p):
                echo "<th>{$p->name}</th>";
              endforeach;
        ?>
      </tr>
      <?php
      foreach ($partners as $i => $r) {
        echo "<tr><th>{$r->name}</th>";

        foreach($products as $j => $p):

          foreach($models as $k => $pr) {
            if($pr->partner_id == $r->id && $pr->product_id == $p->id) {
              $key = $k;
              break;
            }
          }

          echo Html::hiddenInput("PartnerProduct[{$key}][id]", $models[$key]->id);
          echo Html::hiddenInput("PartnerProduct[{$key}][partner_id]", $models[$key]->partner_id);
          echo Html::hiddenInput("PartnerProduct[{$key}][product_id]", $models[$key]->product_id);
          echo "<td>". $form->field($models[$key], "[{$key}]percent")->textInput()->label(false) ."</td>";

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
