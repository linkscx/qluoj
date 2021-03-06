<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Solution;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\polygon\models\Problem */
/* @var $solution \app\modules\polygon\models\PolygonStatus */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['model'] = $model;
$solution->language = Yii::$app->user->identity->language;
?>
<p>
    该页面用于给验题人验证题目数据的准确性，验题前需在
    <?= Html::a(Yii::t('app', 'Tests Data'), ['/polygon/problem/tests', 'id' => $model->id]) ?>
    页面中上传或生成题目的标准输入输出数据。
</p>
<hr>
<?= GridView::widget([
    'layout' => '{items}{pager}',
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive problem-index-list'],
    'columns' => [
        [
            'attribute' => 'id',
            'value' => function ($solution, $key, $index, $column) use ($model) {
                return Html::a($solution->id, [
                    '/polygon/problem/solution-detail',
                    'id' => $model->id,
                    'sid' => $solution->id
                ], ['target' => '_blank']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'who',
            'value' => function ($model, $key, $index, $column) {
                return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->created_by]);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'result',
            'value' => function ($model, $key, $index, $column) {
                if ($model->result == Solution::OJ_CE || $model->result == Solution::OJ_WA
                    || $model->result == Solution::OJ_RE) {
                    return Html::a($model->getResultPolygon(),
                        ['/solution/result', 'id' => $model->id],
                        ['onclick' => 'return false', 'data-click' => "solution_info"]
                    );
                } else {
                    return $model->getResultPolygon();
                }
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'time',
            'value' => function ($model, $key, $index, $column) {
                return $model->time . ' MS';
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'memory',
            'value' => function ($model, $key, $index, $column) {
                return $model->memory . ' KB';
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'language',
            'value' => function ($solution, $key, $index, $column) use ($model) {
                return Html::a($solution->getLang(), [
                '/polygon/problem/solution-detail',
                    'id' => $model->id,
                    'sid' => $solution->id
                ], ['target' => '_blank']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'created_at',
            'value' => function ($model, $key, $index, $column) {
                return Html::tag('span', Yii::$app->formatter->asRelativeTime($model->created_at), ['title' => $model->created_at]);
            },
            'format' => 'raw'
        ]
    ],
]); ?>
<hr>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($solution, 'language')->dropDownList(Solution::getLanguageList()) ?>

    <?= $form->field($solution, 'source')->widget('app\widgets\codemirror\CodeMirror'); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

