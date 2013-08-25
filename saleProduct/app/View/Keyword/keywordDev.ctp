<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>关键字计划编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');

		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');

		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('modules/keyword/editKeyword');
		
		$keyword  = ClassRegistry::init("Keyword") ;
		
		//获取任务主关键字
		
		$taskId = $params['arg1'] ;
	?>
  
</head>

<style>
	.dev-container{
		overflow:auto;
	}	
	
	.dev-item{
		float:left;
		width:345px;
		height:300px;
		margin:3px;
		border:1px solid #CCC;
		padding:3px;
		overflow:auto;
	}
	
	.action-td{
		width:40px;
	}
	
	.action-td img{
		cursor:pointer;
	}
	
	.num-td{
		width:20px;
	}
</style>

<script>
		var taskId = '<?php echo $taskId;?>' ;
</script>

<body class="container-popup">
	<div class="toolbar toolbar-auto plan-t">
				<table>
					<tr>
						<td>
							<input type="text" id="mainKeyword" class="input-larger "  placeHolder="输入主关键字"/>
						</td>					
						<td class="toolbar-btns" rowspan="3">
							<button class="query-btn btn btn-primary asyn-keyword"   >获取Semrush关键字</button>
							&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
				</table>
		</div>
		<div class="dev-container">
			<div class="dev-item main-keyword">
			</div>
		</div>
		<div class="dev-niche">
			<div class="niche-grid"></div>
		</div>
</body>
</html>