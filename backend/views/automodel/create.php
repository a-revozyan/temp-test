<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Automodel */

$this->title = Yii::t('app', 'Create Automodel');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Automodels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="automodel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
