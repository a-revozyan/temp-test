<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelRiskCategory */

$this->title = 'Create Travel Risk Category';
$this->params['breadcrumbs'][] = ['label' => 'Travel Risk Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-risk-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
