<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AutoRiskType */

$this->title = 'Update Auto Risk Type: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Auto Risk Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auto-risk-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
