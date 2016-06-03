<?php
if(!defined('IN_DISCUZ')) {	exit('Access Denied');}
if($magnet = base64_decode(trim($_GET["magnet"]))){
    dheader("Location:{$magnet}");
};
?>