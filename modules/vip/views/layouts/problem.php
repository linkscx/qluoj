<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\bootstrap\Nav;
$model = $this->params['model'];
?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="col-md-2">
    <?= Nav::widget([
        'options' => ['class' => 'nav nav-pills nav-stacked'],
        'items' => [
            ['label' => Yii::t('app', 'Problem'), 'url' => ['/vip/problem/index']]
        ],
    ]) ?>
</div>
<div class="col-md-10">
    <div class="problem-header">
        <?= \yii\bootstrap\Nav::widget([
            'options' => ['class' => 'nav nav-pills'],
            'items' => [
                ['label' => Yii::t('app', 'Preview'), 'url' => ['/vip/problem/view', 'id' => $model->id]],
                ['label' => Yii::t('app', 'Edit'), 'url' => ['/vip/problem/update', 'id' => $model->id]],
                ['label' => '题解', 'url' => ['/vip/problem/solution', 'id' => $model->id]],
                ['label' => Yii::t('app', 'Tests Data'), 'url' => ['/vip/problem/test-data', 'id' => $model->id]],
                ['label' => Yii::t('app', 'Verify Data'), 'url' => ['/vip/problem/verify', 'id' => $model->id]],
                ['label' => Yii::t('app', 'SPJ'), 'url' => ['/vip/problem/spj', 'id' => $model->id]],
                ['label' => Yii::t('app', 'Subtask'), 'url' => ['/vip/problem/subtask', 'id' => $model->id]]
            ],
        ]) ?>
    </div>
    <hr>
    <?= $content ?>
</div>
<?php $this->endContent(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        // 连接服务端
        var socket = io(document.location.protocol + '//' + document.domain + ':2120');
        var uid = <?= Yii::$app->user->isGuest ? session_id() : Yii::$app->user->id ?>;
        // 连接后登录
        socket.on('connect', function () {
            socket.emit('login', uid);
        });
        // 后端推送来消息时
        socket.on('msg', function (msg) {
            alert(msg);
        });
    })
</script>
