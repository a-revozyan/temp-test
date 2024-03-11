<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Partner group type coefficients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-bordered">
      <tr>
        <th>Partners/Group types</th>
        <?php foreach($group_types as $p):
                echo "<th>{$p->name_ru}</th>";
              endforeach;
        ?>
      </tr>
      <?php
      foreach ($partners as $i => $r) {
        echo "<tr><th>{$r->name}</th>";

        foreach($group_types as $j => $p):

          foreach($models as $k => $pr) {
            if($pr->partner_id == $r->id && $pr->group_type_id == $p->id) {
              $key = $k;
              break;
            }
          }

          echo Html::hiddenInput("TravelPartnerGroupType[{$key}][id]", $models[$key]->id);
          echo Html::hiddenInput("TravelPartnerGroupType[{$key}][partner_id]", $models[$key]->partner_id);
          echo Html::hiddenInput("TravelPartnerGroupType[{$key}][group_type_id]", $models[$key]->group_type_id);
          echo "<td>". $form->field($models[$key], "[{$key}]coeff")->textInput()->label(false) ."</td>";

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
