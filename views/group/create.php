<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = Yii::t('app', 'Create Group' . ' - ' .  Yii::$app->setting->get('ojName'));
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if((Yii::$app->user->identity->isVip() || Yii::$app->user->identity->isAdmin())) : ?>
    <div class="group-create">

        <?= '<h1>' . Html::encode($this->title) . '</h1>' ?>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
<?php else : echo "You are not allowed to perform this action."; endif;?>
