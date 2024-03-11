<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelProgramPeriod */

$this->title = Yii::t('app', 'Update Travel Program Period: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Program Periods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="travel-program-period-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
