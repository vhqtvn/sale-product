<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/publish/ebay_publish_history');
		
		$groupCode = $user["GROUP_CODE"] ;
		$loginId = $user['LOGIN_ID'] ;
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		
		$templateId = $params['arg1'] ;

	?>
	<script type="text/javascript">
		var templateId = '<?php echo $templateId;?>'
	</script>

	 <style type="text/css">
		img{
			cursor:pointer;
		}
	</style>

</head>
<body>

	<div class="grid-content" style="margin-top:5px;">
	</div>
</body>
</html>
