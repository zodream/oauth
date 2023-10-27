<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->registerCssFile('@oauth.css')
    ->registerJsFile('@oauth.min.js');
?>
<!DOCTYPE html>
<html lang="<?=$this->get('language', 'zh-CN')?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="Description" content="<?=$this->description?>" />
    <meta name="keywords" content="<?=$this->keywords?>" />
    <title><?=$this->title?></title>
    <?=$this->header();?>
</head>
<body>
    <?=$this->contents()?>
    <?=$this->footer()?>
</body>
</html>