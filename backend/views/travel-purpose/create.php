<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelPurpose */

$this->title = Yii::t('app', 'Create Travel Purpose');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Purposes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-purpose-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
