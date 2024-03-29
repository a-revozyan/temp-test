<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Autocomp */

$this->title = Yii::t('app', 'Create Autocomp');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Autocomps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="autocomp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
