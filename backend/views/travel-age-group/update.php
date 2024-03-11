<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelAgeGroup */

$this->title = Yii::t('app', 'Update Travel Age Group: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Age Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="travel-age-group-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
