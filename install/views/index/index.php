<?php


use yii\helpers\Html;

$this->title = 'index';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        这是创建的Index页面<?php var_dump($data->name)?>
    </p>

    <code><?= __FILE__ ?></code>
</div>
