<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NumberDrivers */

$this->title = Yii::t('app', 'Create Number Drivers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Number Drivers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="number-drivers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
