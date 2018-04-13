<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->registerJsFile('@oauth.min.js');
?>
   <?=$this->footer()?>
   </body>
</html>