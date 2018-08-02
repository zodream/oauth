<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
use Zodream\Domain\Access\Auth;
/** @var $this View */
$this->title = '授权登录';
$this->extend('layouts/header');
?>

<div class="main-box">
    <div class="user-box">
        <a href="javascript:document.getElementById('scope-form').submit();">
            <img src="<?=auth()->user()->avatar?>" alt="">
        </a>
    </div>
    <div class="scope-box">
        <form id="scope-form" action="<?=$this->url()?>" method="post">
            <div class="input-check">
                <input type="checkbox" name="" id="" checked> 全选
            </div>
            <div class="input-check">
                <input type="checkbox" name="" id="" checked> 基本信息
            </div>
        </form>
    </div>
</div>

<?php $this->extend('layouts/footer');?>