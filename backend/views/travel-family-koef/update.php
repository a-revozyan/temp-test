<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelFamilyKoef */

$this->title = 'Update Travel Family Koef: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Travel Family Koefs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="travel-family-koef-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'partner' => $partner,
    ]) ?>

</div>
