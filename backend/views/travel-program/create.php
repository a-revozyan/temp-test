<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelProgram */

$this->title = Yii::t('app', 'Create Travel Program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-program-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
