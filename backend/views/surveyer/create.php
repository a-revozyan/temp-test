<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Surveyer */

$this->title = 'Create Surveyer';
$this->params['breadcrumbs'][] = ['label' => 'Surveyers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="surveyer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
