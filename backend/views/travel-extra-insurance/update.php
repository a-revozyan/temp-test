<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelExtraInsurance */

$this->title = Yii::t('app', 'Update Travel Extra Insurance: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Extra Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="travel-extra-insurance-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
