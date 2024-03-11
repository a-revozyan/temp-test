<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Country;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\widgets\ActiveForm */

$this->title = $partner->name . ' ' . Yii::t('app', 'Program risk amounts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Partners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); 

    ?>

    <table class="table table-bordered">
      <tr>
        <th>Risks/Programs</th>
        <?php foreach($programs as $p):
                echo "<th>{$p->name}</th>";
              endforeach;
        ?>
      </tr>
      <?php
      foreach ($risks as $i => $r) {
        echo "<tr><th>{$r->name_ru}</th>";

        foreach($programs as $j => $p):

          foreach($program_risks as $k => $pr) {
            if($pr->program_id == $p->id && $pr->risk_id == $r->id) {
              $key = $k;
              break;
            }
          }

          echo Html::hiddenInput("TravelProgramRisk[{$key}][id]", $program_risks[$key]->id);
          echo Html::hiddenInput("TravelProgramRisk[{$key}][program_id]", $program_risks[$key]->program_id);
          echo Html::hiddenInput("TravelProgramRisk[{$key}][risk_id]", $program_risks[$key]->risk_id);
          echo "<td>". $form->field($program_risks[$key], "[{$key}]amount")->textInput()->label(false) ."</td>";

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
