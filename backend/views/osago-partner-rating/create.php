<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OsagoPartnerRating */

$this->title = Yii::t('app', 'Create Osago Partner Rating');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Osago Partner Ratings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="osago-partner-rating-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
