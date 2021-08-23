<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $contests array */
/* @var $news app\models\Discuss */

$this->title = Yii::$app->setting->get('ojName');
?>
<div class="row blog">
    <div class="col-md-8">
        <div class="jumbotron">
            <h1>Welcome!!!</h1>
            <p>欢迎来到<?= Yii::$app->setting->get('schoolName') ?>在线判题系统——QLU Online Judge</p>
            <img src="https://s3.ax1x.com/2021/01/29/yi73o4.jpg" width=400px height=300px />
        </div>
        <hr>
        <div class="blog-main">
            <?php foreach ($news as $v): ?>
                <div class="blog-post">
                    <h2 class="blog-post-title"><?= Html::a(Html::encode($v['title']), ['/site/news', 'id' => $v['id']]) ?></h2>
                    <p class="blog-post-meta">
                        <span class="glyphicon glyphicon-time"></span> <?= Yii::$app->formatter->asDate($v['created_at']) ?></p>
                </div>
            <?php endforeach; ?>
            <?= \yii\widgets\LinkPager::widget([
                'pagination' => $pages,
            ]); ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sidebar-module sidebar-module-inset">
            <h4>关于齐鲁工业大学（山东省科学院）</h4>
            <p>齐鲁工业大学（山东省科学院）坐落于国家历史文化名城——泉城济南，是山东省重点建设的应用研究型大学，山东省最大的综合性自然科学研究机构，山东省属高校高水平大学“冲一流”建设高校。</p>
        </div>
        <?php if (!empty($contests)): ?>
        <div class="sidebar-module">
            <h4>最近比赛</h4>
            <ol class="list-unstyled">
                <?php foreach ($contests as $contest): ?>
                <li>
                    <?= Html::a(Html::encode($contest['title']), ['/contest/view', 'id' => $contest['id']]) ?>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>
        <?php if (!empty($discusses)): ?>
            <div class="sidebar-module">
                <h4>最近讨论</h4>
                <ol class="list-unstyled">
                    <?php foreach ($discusses as $discuss): ?>
                        <li class="index-discuss-item">
                            <div>
                                <?= Html::a(Html::encode($discuss['title']), ['/discuss/view', 'id' => $discuss['id']]) ?>
                            </div>
                            <small class="text-muted">
                                <span class="glyphicon glyphicon-user"></span>
                                <?= Html::a(Html::encode($discuss['nickname']), ['/user/view', 'id' => $discuss['username']]) ?>
                                &nbsp;•&nbsp;
                                <span class="glyphicon glyphicon-time"></span> <?= Yii::$app->formatter->asRelativeTime($discuss['created_at']) ?>
                                &nbsp;•&nbsp;
                                <?= Html::a(Html::encode($discuss['ptitle']), ['/problem/view', 'id' => $discuss['pid']]) ?>
                            </small>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
    </div>
</div>
