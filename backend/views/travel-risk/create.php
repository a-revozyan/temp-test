<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelRisk */

$this->title = Yii::t('app', 'Create Travel Risk');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Risks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-risk-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
