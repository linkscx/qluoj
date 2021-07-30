<?php

use yii\bootstrap\Nav;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = 'Wiki';
?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="row">
    <div class="col-md-3">
        <?= Nav::widget([
            'items' => [
                ['label' => Yii::t('app', 'OJ  Information'), 'url' => ['wiki/index']],
                ['label' => Yii::t('app', 'Contest'), 'url' => ['wiki/contest']],
                ['label' => 'Requirements For Creating Problems', 'url' => ['wiki/problem']],
                ['label' => Yii::t('app', 'Special Judge'), 'url' => ['wiki/spj']],
                ['label' => Yii::t('app', 'OI Mode'), 'url' => ['wiki/oi']],
		['label' => Yii::t('app', 'About'), 'url' => ['wiki/about']],
		['label' => 'Update Logs', 'url' => ['wiki/updatelog']]
            ],
            'options' => ['class' => 'nav nav-pills nav-stacked']
        ]) ?>
    </div>
    <div class="col-md-9">
        <div class="wiki-contetn">
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endContent(); ?>

