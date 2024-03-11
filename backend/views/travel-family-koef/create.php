<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelFamilyKoef */

$this->title = 'Create Travel Family Koef';
$this->params['breadcrumbs'][] = ['label' => 'Travel Family Koefs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-family-koef-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'partner' => $partner,
    ]) ?>

</div>
