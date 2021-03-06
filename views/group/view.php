<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\GroupUser;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */

$this->title = $model->name . ' - ' .  Yii::$app->setting->get('ojName');
$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
?>
<div class="group-view">
    <div class="row">
        <div class="col-md-3">
            <h2><?= Html::a(Html::encode($this->title), ['/group/view', 'id' => $model->id]) ?></h2>
            <?php if (!Yii::$app->user->isGuest && ($model->role == GroupUser::ROLE_LEADER || Yii::$app->user->identity->isAdmin())): ?>
            <?= Html::a(Yii::t('app', 'Setting'), ['/group/update', 'id' => $model->id], ['class' => 'btn btn-default btn-block']) ?>
            <?php endif; ?>
            <hr>
            <p>
                <?= Yii::$app->formatter->asMarkdown($model->description); ?>
            </p>
            <hr>
            <p><?= Yii::t('app', 'Join Policy') ?>: <?= $model->getJoinPolicy() ?></p>
	    <p><?= Yii::t('app', 'Status') ?>: <?= $model->getStatus() ?></p>
	    <hr>
	    <p>网站&账号&题目问题请联系管理员<br>QQ: 1098509291<br>WeChat: linkscx<br>Email: linkscx@163.com</p>
        </div>
        <div class="col-md-9">
            <div>
                <h2 style="display: inline">
                    <?= Yii::t('app', 'Homework'); ?>
                </h2>
                <?php if ($model->hasPermission()): ?>
                <?php Modal::begin([
                    'header' => '<h3>' . Yii::t('app', 'Create') . '</h3>',
                    'toggleButton' => [
                        'label' => Yii::t('app', 'Create'),
                        'tag' => 'a',
                        'style' => 'cursor:pointer;'
                    ]
                ]); ?>
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($newContest, 'title')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
                    <?= $form->field($newContest, 'start_time')->widget('app\widgets\laydate\LayDate', [
                        'clientOptions' => [
                            'istoday' => true,
                            'type' => 'datetime'
                        ],
                        'options' => ['autocomplete' => 'off']
                    ]) ?>
                    <?= $form->field($newContest, 'end_time')->widget('app\widgets\laydate\LayDate', [
                        'clientOptions' => [
                            'istoday' => true,
                            'type' => 'datetime'
                        ],
                        'options' => ['autocomplete' => 'off']
                    ]) ?>

                    <?= $form->field($newContest, 'lock_board_time')->widget('app\widgets\laydate\LayDate', [
                        'clientOptions' => [
                            'istoday' => true,
                            'type' => 'datetime'
                        ]
                    ])->hint("如果不需要封榜请留空，当前会在比赛结束{$scoreboardFrozenTime}小时后才会自动在前台页面解除封榜限制。
                        如需提前结束封榜也可选择清空该表单项。
                    ") ?>

                    <?= $form->field($newContest, 'type')->radioList([
                        Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
                        Contest::TYPE_RANK_GROUP => Yii::t('app', 'ICPC'),
                        Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
                        Contest::TYPE_OI => Yii::t('app', 'OI'),
                        Contest::TYPE_IOI => Yii::t('app', 'IOI'),
                    ])->hint('不同类型的区别只在于榜单的排名方式。详见：' . Html::a('比赛类型', ['/wiki/contest'], ['target' => '_blank'])) ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php Modal::end(); ?>
                <?php endif; ?>
            </div>
            <?= GridView::widget([
                'layout' => '{items}{pager}',
                'dataProvider' => $contestDataProvider,
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'value' => function ($model, $key, $index, $column) {
                            return Html::a(Html::encode($model->title), ['/contest/view', 'id' => $key]);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model, $key, $index, $column) {
                            $link = Html::a(Yii::t('app', 'Register »'), ['/contest/register', 'id' => $model->id]);
                            if (!Yii::$app->user->isGuest && $model->isUserInContest()) {
                                $link = '<span class="well-done">' . Yii::t('app', 'Registration completed') . '</span>';
                            }
                            if ($model->status == Contest::STATUS_VISIBLE &&
                                !$model->isContestEnd() &&
                                $model->scenario == Contest::SCENARIO_ONLINE) {
                                $column = $model->getRunStatus(true) . ' ' . $link;
                            } else {
                                $column = $model->getRunStatus(true);
                            }
                            $userCount = $model->getContestUserCount();
                            return $column . ' ' . Html::a(' <span class="glyphicon glyphicon-user"></span>x'. $userCount, ['/contest/user', 'id' => $model->id]);
                        },
                        'format' => 'raw',
                        'options' => ['width' => '220px']
                    ],
                    [
                        'attribute' => 'start_time',
                        'options' => ['width' => '150px']
                    ],
                    [
                        'attribute' => 'end_time',
                        'options' => ['width' => '150px']
                    ],
                ],
            ]); ?>

            <div>
                <h2>
                    <?= Yii::t('app', 'Member'); ?>
		</h2>
		<?php if ($model->hasPermission()): ?>
		    <?php Modal::begin([
                        'header' => '<h2>' . Yii::t('app', 'Add Members') . '</h2>',
                        'toggleButton' => ['label' => Yii::t('app', 'Add Members'), 'class' => 'btn btn-success'],
                    ]);?>
                    <?php $form = ActiveForm::begin(['options' => ['target' => '_blank']]); ?>
			<p class="hint-block">一个用户占据一行，每行格式为<code>username</code></p>
			<p class="hint-block">请注意，输入的是<code>username</code>，而不是<code>nickname</code></p>

                        <?= $form->field($newGroupUser, 'username')->textarea(['rows' => 10])  ?>

                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                    <?php Modal::end(); ?>
		<?php endif; ?>
		<?php if($model->hasPermission()): ?>
			<?php Modal::begin([
				'header' => '<h2>Export Grade</h2>',
				'toggleButton' => ['label' =>'Export Grade', 'class' => 'btn btn-success'],
			]);?>
			<p>该功能尚在开发中..</p>
			<?php Modal::end(); ?>
		<?php endif; ?>
	    </div>
	    <br>
            <?= GridView::widget([
                'layout' => '{items}{pager}',
                'dataProvider' => $userDataProvider,
                'options' => ['class' => 'table-responsive'],
                    'pager' => [//自定义分页样式
                    'firstPageLabel' => 'First',
                    'lastPageLabel' => 'Last'
                ],
                'columns' => [
                    [
                        'attribute' => 'role',
                        'value' => function ($model, $key, $index, $column) {
                            return $model->getRole(true);
                        },
                        'format' => 'raw',
                        'options' => ['width' => '80px']
		    ],
		    [
			'attribute' => 'Username',
			'value' => function($model, $key, $index, $column) {
			    return Html::a(Html::encode($model->user->username), ['/user/view', 'id' => $model->user->id]);
			},
			'format' => 'raw',
		    ],
                    [
                        'attribute' => Yii::t('app', 'Nickname'),
                        'value' => function ($model, $key, $index, $column) {
                            return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model, $key, $index, $column) {
                            return Yii::$app->formatter->asRelativeTime($model->created_at);
                        },
                        'options' => ['width' => '150px']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{user-update} {user-delete}',
                        'buttons' => [
                            'user-update' => function ($url, $model, $key) {
                                $options = [
                                    'title' => Yii::t('yii', 'Update'),
                                    'aria-label' => Yii::t('yii', 'Update'),
                                    'data-pjax' => '0',
                                    'onclick' => 'return false',
                                    'data-click' => "user-manager"
                                ];
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                            },
                            'user-delete' => function ($url, $model, $key) {
                                $options = [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ];
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                            }
                        ],
                        'visible' => $model->hasPermission(),
                        'options' => ['width' => '90px']
                    ]
                ],
            ]); ?>
        </div>
    </div>
</div>
<?php
$js = <<<EOF
$('[data-click=user-manager]').click(function() {
    $.ajax({
        url: $(this).attr('href'),
        type:'post',
        error: function(){alert('error');},
        success:function(html){
        $('#solution-content').html(html);
        $('#solution-info').modal('show');
    }
    });
});
EOF;
$this->registerJs($js);
?>
<?php Modal::begin([
    'options' => ['id' => 'solution-info']
]); ?>
    <div id="solution-content">
    </div>
<?php Modal::end(); ?>
