<?php
$this->title = "Backend" . ' - ' .  Yii::$app->setting->get('ojName');
?>
<div class="vip-default-index">
    <h3>Hello, <?= Yii::$app->user->identity->nickname ?>, 您可以点击左侧Problem审核题目</h3>
</div>
<hr>
