<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="problem-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'time_limit', [
        'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-addon\">s</span></div>",
    ])->textInput(['maxlength' => 128, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'memory_limit', [
        'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-addon\">MByte</span></div>",
    ])->textInput(['maxlength' => 128, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'description')->widget('app\widgets\editormd\Editormd') ?>

    <?= $form->field($model, 'input')->widget('app\widgets\editormd\Editormd') ?>

    <?= $form->field($model, 'output')->widget('app\widgets\editormd\Editormd') ?>

    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'sample_input')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sample_output')->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'sample_input_2')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sample_output_2')->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'sample_input_3')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sample_output_3')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <hr>

    <?= $form->field($model, 'spj')->radioList([
        '1' => Yii::t('app', 'Yes'),
        '0' => Yii::t('app', 'No')
    ])?>

    <?= $form->field($model, 'hint')->widget('app\widgets\editormd\Editormd') ?>

    <?= $form->field($model, 'tags')->textarea(['maxlength' => true, 'placeholder' => '请标明题目考察的主要知识点'])
        ->hint('请为题目添加知识点标签。多标签用半角的逗号隔开。<br>如果是入门简单题，请填：C语言入门；如果是数据结构题，请填数据结构的名字；如果是某个算法，请填算法的名字；如果是成套的比赛题，请填套题的名字。<br>示例：C语言入门,栈,dfs,bfs,dp,暴力,贪心,最短路,2021牛客暑期多校训练营10') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
