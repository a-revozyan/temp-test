<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelAgeGroup */

$this->title = Yii::t('app', 'Create Travel Age Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Age Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-age-group-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
