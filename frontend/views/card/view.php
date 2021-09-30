<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Card */

$this->title = $model->number;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="card-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'series',
            'number',
            'date_begin:datetime',
            'date_end:datetime',
            'date_use:datetime',
            'sum',
            ['attribute' => 'status', 'value' => function($model){
                if ($model->status == 0){
                    $status = 'Not Active';
                } else $model->status == 1 ? $status = 'Active' : $status = 'Expired';
                return $status;
            }],
            ['attribute' => 'purchase', 'value' => $model->Purchase($model)],
        ],
    ]) ?>


</div>
