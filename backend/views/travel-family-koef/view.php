<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TravelFamilyKoef */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Travel Family Koefs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="travel-family-koef-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'partner_id',
            'members_count',
            'koef',
        ],
    ]) ?>

</div>
