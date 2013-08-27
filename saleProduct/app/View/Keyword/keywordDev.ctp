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
		
		$security  = ClassRegistry::init("Security") ;
		$loginId = $user['LOGIN_ID'] ;
		
		$audit_niche 								= $security->hasPermission($loginId , 'audit_niche') ;
		$assign_kw_charger                      = $security->hasPermission($loginId , 'assign_kw_charger') ;//回退
		$kw_relation_product				= $security->hasPermission($loginId , 'kw_relation_product') ;
		$add_kw_plan						= $security->hasPermission($loginId , 'add_kw_plan') ;
		$add_kw_task							= $security->hasPermission($loginId , 'add_kw_task') ;
		$niche_kw_dev							= $security->hasPermission($loginId , 'niche_kw_dev') ;
	?>
  
</head>

<style>
	.dev-container{
		overflow:auto;
	}	
	

	.action-td{
		width:60px;
	}
	
	.action-td img{
		cursor:pointer;
	}
	
	.num-td{
		width:40px;
	}
	
	
</style>

<script>
		var taskId		= '<?php echo $taskId;?>' ;
		var isDev		= <?php echo $niche_kw_dev?"true":"false"?> ;
</script>

<body class="container-popup">
<?php if( $niche_kw_dev ){?>
	<div class="toolbar toolbar-auto plan-t">
				<table>
					<tr>
						<td>
							<input type="text" id="mainKeyword" class="input-larger "  placeHolder="输入主关键字"/>
						</td>					
						<td class="toolbar-btns" rowspan="3">
							<button class="query-btn btn btn-primary asyn-keyword"   >获取扩展关键字</button>
							&nbsp;&nbsp;&nbsp;
						</td>
						<td class="toolbar-filter" style="text-align: right;">
							搜索量>=<input type="text"  name="search_volume" style="width:70px;"/>
							CPC>=<input type="text" name="cpc"    style="width:70px;"/>
							竞争>=<input type="text"   name="competition" style="width:70px;"/>
							搜索结果>=<input type="text"  name="result_num"   style="width:70px;"/>
							<button class="query-btn btn  btn-query" >查询</button>
							<button class="query-btn btn btn-primary  btn-filter" >筛选</button>
							&nbsp;&nbsp;&nbsp;
						</td>
					</tr>
				</table>
		</div>
<?php } ?>
		<div class="row-fluid">
			<div class="span6">
					<div class="dev-item main-keyword" >
					</div>
					<div class="dev-item child-keyword" style="margin-top:10px;">
					</div>
			</div>
			<div class="span6">
				<div class="dev-niche">
					<div class="niche-grid"></div>
				</div>
			</div>
		</div>
</body>
</html>