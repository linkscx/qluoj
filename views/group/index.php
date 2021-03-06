<?php

use yii\bootstrap\Nav;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Groups' . ' - ' .  Yii::$app->setting->get('ojName'));
?>
<?= Nav::widget([
    'items' => [
        [
            'label' => Yii::t('app', 'My Groups'),
            'url' => ['group/my-group'],
            'visible' => !Yii::$app->user->isGuest
        ],
        [
            'label' => Yii::t('app', 'All Groups'),
            'url' => ['group/index']
        ],
        [
            'label' => Yii::t('app', 'Create'),
            'url' => 'create',
            'visible' => (!Yii::$app->user->isGuest && (Yii::$app->user->identity->isVip() || Yii::$app->user->identity->isAdmin())),
            'options' => ['class' => 'pull-right']
        ]
    ],
    'options' => ['class' => 'nav-tabs', 'style' => 'margin-bottom: 15px']
]) ?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_group_item',
    'layout' => '<div class="card-columns">{items}</div>{summary}{pager}'
])?>
