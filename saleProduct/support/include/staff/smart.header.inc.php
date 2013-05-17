<? if(!defined('OSTSCPINC') || !is_object($thisuser) || !$thisuser->isStaff() || !is_object($nav)) die('Access Denied'); ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php
if(defined('AUTO_REFRESH') && is_numeric(AUTO_REFRESH_RATE) && AUTO_REFRESH_RATE>0){ //Refresh rate
echo '<meta http-equiv="refresh" content="'.AUTO_REFRESH_RATE.'" />';
}
?>
<title>osTicket :: Staff Control Panel</title>

<link rel="stylesheet" href="css/style.css" media="screen">
<link rel="stylesheet" href="css/tabs.css" type="text/css">
<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="../../app/webroot/css/default/style.css" />
<style>
	td{
	font-size:12px;
	}
</style>
<script type="text/javascript" src="../../app/webroot/js/jquery.js"></script>
<script type="text/javascript" src="../../app/webroot/js/common.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/scp.js"></script>
<script type="text/javascript" src="js/tabber.js"></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="dtree_1.js"></script>
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
<?php
if($cfg && $cfg->getLockTime()) { //autoLocking enabled.?>
<script type="text/javascript" src="js/autolock.js" charset="utf-8"></script>
<?}?>
</head>
<body>
<?php
if($sysnotice){?>
<div id="system_notice"><?php echo $sysnotice; ?></div>
<?php 
}?>
<div id="container">
    <div id="content" width="100%">

