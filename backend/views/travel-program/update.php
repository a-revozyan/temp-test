<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelProgram */

$this->title = Yii::t('app', 'Update Travel Program: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="travel-program-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
