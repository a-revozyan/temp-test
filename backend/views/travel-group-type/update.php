<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelGroupType */

$this->title = Yii::t('app', 'Update Travel Group Type: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Group Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="travel-group-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
