<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelMultiplePeriod */

$this->title = 'Create Travel Multiple Period';
$this->params['breadcrumbs'][] = ['label' => 'Travel Multiple Periods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-multiple-period-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'partner' => $partner,
    ]) ?>

</div>
