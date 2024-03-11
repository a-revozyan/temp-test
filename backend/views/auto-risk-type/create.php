<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AutoRiskType */

$this->title = 'Create Auto Risk Type';
$this->params['breadcrumbs'][] = ['label' => 'Auto Risk Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auto-risk-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
