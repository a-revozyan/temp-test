<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OsagoPartnerRating */

$this->title = Yii::t('app', '{partner} OSAGO Rating', [
    'partner' => $model->partner->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Osago Partner Ratings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="osago-partner-rating-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
