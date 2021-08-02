<?php
	$content = file_get_contents("../README.md");
?>
<div class="news-content">
	<?= Yii::$app->formatter->asMarkdown($content) ?>
</div>
