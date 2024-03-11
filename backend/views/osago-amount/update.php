<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OsagoAmount */

$this->title = Yii::t('app', 'Update Osago Amount: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Osago Amounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="osago-amount-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
