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
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('bootstrap/bootstrap-tooltip');

		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('modules/keyword/developer');

		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		
		
		
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
		$niche_kw_dev_filter							= $security->hasPermission($loginId , 'niche_kw_dev_filter') ;
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

		$(function(){
		//	$("[name='search_content']").attr("title","输入示例<br><nobr>同时包括多个关键字(And):XXX1,XXX2</nobr><br>多个关键字之一(Or):XXX1|XXX2").tooltip({placement:'bottom'}) ;
		}) ;
		/*
us  www.amazon.com
uk  www.amzon.co.uk
ca www.amazon.ca
ru
		*/
</script>

<body class="container-popup">
<?php if( $niche_kw_dev ){?>
	<div class="toolbar toolbar-auto plan-t">
				<table>
					<tr>
						<td>
							<input type="text" id="mainKeyword" style="width:160px;" placeHolder="输入主关键字"/>
						</td>
						<td>
							<select  id="site" style="width:110px;">
								<option value="us">Google.com</option>
								<option value="uk">Google.co.uk</option>
								<option value="ca">Google.ca</option>
								<option value="ru">Google.ru</option>
								<option value="de">Google.de</option>
								<option value="fr">Google.fr</option>
								<option value="es">Google.es</option>
								<option value="it">Google.it</option>
								<option value="br">Google.br</option>
								<option value="au">Google.com.au</option>
								<option value="us.bing">Bing.com</option>
							</select>
						</td>
						<td>
							<select  id="total" style="width:70px;"   data-widget="tooltip" data-options="{placement:'bottom',title:'每次扩展最多扩展关键字数量！'}">
								<option value="100">100</option>
								<option value="200">200</option>
								<option value="500">500</option>
								<option value="1000">1000</option>
								<option value="2000">2000</option>
								<option value="5000">5000</option>
							</select>
						</td>					
						<td class="toolbar-btns" rowspan="3">
							<button class="query-btn btn btn-primary asyn-keyword"   >获取</button>
							&nbsp;&nbsp;&nbsp;
						</td>
						<td class="toolbar-filter" style="text-align: right;">
							包含<input    type="text"  name="search_content" style="width:140px;"  data-widget="tooltip" data-options="{placement:'bottom',title:'包括多个关键字(And):XXX1,XXX2;多个关键字之一(Or):XXX1|XXX2'}"/>
							搜索量>=<input type="text"  name="search_volume" style="width:60px;"/>
							CPC<=<input type="text" name="cpc"    style="width:60px;"/>
							竞争<=<input type="text"   name="competition" style="width:60px;"/>
							<button class="query-btn btn  btn-query" >查询</button>
							<?php if($niche_kw_dev_filter){ ?>
							<button class="query-btn btn btn-primary  btn-filter" >筛选</button>
							<?php }?>
						</td>
					</tr>
				</table>
		</div>
<?php } ?>
		<div class="row-fluid">
			<div class="span6">
					<div class="dev-item main-keyword" >
					</div>
			</div>
			<div class="span6">
					<div class="dev-item child-keyword"  >
					</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div class="dev-niche">
					<div class="niche-grid"></div>
				</div>
			</div>
		</div>
</body>
</html>