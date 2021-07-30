<?php
//file added by scx
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', 'Import Problem');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$maxFileSize = min(ini_get("upload_max_filesize"),ini_get("post_max_size"));
?>
<div class="problem-import">

    <h1><?= Html::encode($this->title) ?></h1>
    <b>上传说明：本OJ支持以下两种格式的题目导入</b>
    <p>1.支持从domjudge导出的题目(或自行构建压缩文件)上传，文件类型为zip。</p>
    <p>&emsp;压缩包内请务必放置题目描述(.pdf文件)和测试数据(.in|.out|.ans文件均可)，导入题目以压缩包文件名作为题目名称。</p>
    <p>&emsp;题目默认时间限制1s、空间限制128M，默认不开启Special Judge，如有需要请至题目描述页面人工修改。</p>
    <p>2.支持从hustoj导出的题目上传，文件类型为xml。</p>
    <br>
    <p><b>注意：题目上传成功后，请务必在'Verify Data'中用标程测试一下所上传的数据。</b></p>
    <hr>
    <?php if (extension_loaded('xml')): ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'problemFile')->fileInput()
        ->hint("提交文件为zip或xml格式，
        文件限制大小：{$maxFileSize}，该限制为系统限制，如需修改该大小限制，请修改php.ini文件的post_max_size、upload_max_filesize选项。")?>

    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end() ?>
    <?php else: ?>
        <p>服务器尚未开启 php-xml 扩展，请安装 php-xml 后再使用此功能。</p>
    <?php endif; ?>
</div>
