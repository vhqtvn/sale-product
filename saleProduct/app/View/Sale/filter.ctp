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
		echo $this->Html->script('modules/sale/filter');
		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$PDT_CREATE = $security->hasPermission($loginId , 'PDT_CREATE') ;
		$PDT_UPDATE = $security->hasPermission($loginId , 'PDT_UPDATE') ;
	?>
  
   <script type="text/javascript">
    var $PDT_CREATE = <?php echo $PDT_CREATE?'true':'false' ;?> ;
	var $PDT_UPDATE = <?php echo $PDT_UPDATE?'true':'false' ;?> ;
	var $loginId = '<?php echo $loginId;?>' ;
   </script>


</head>
<body>
	<div class="toolbar toolbar-auto">
		<table>
			<tr>
				<th>
					任务编号:
				</th>
				<td>
					<input type="text" id="taskCode"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-task',qc:'.toolbar'}">查询</button>
					<?php if( $PDT_CREATE ){ ?>
					<button class="create-task btn">创建开发任务</button>
					<?php }?>
				</td>
			</tr>						
		</table>					

	</div>	
	<div class="grid-content" style="width:99.5%">
	</div>
	
	<div class="grid-content-details" style="width:99.5%;margin-top:5px;">
	</div>
</body>
</html>
