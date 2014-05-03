<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>入库计划列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('modules/warehouse/in/lists');
		echo $this->Html->script('modules/warehouse/in-flow');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$security  = ClassRegistry::init("Security") ;
		$loginId   = $user['LOGIN_ID'] ;
		
		$hasEditPermission = $security->hasPermission($loginId , 'IN_STATUS0') ;
	?>
	
	<style>

   		.ui-tabs .ui-tabs-nav li a{
   			padding:0px 8px!important;
   		}
	</style>
		<style type="">
   		div.flow-node{
			cursor: pointer ;
			/*width:60px;
   			word-wrap: break-word;
   			height:35px;*/
   		}
   		
		div.flow-node {
			border: 2px solid #0FF;
   			float:left ;
			margin-bottom:5px;   	
			padding:1px  3px;
			color:#000;	
		}
		
		div.flow-split{
			float:left ;
			/*display:none;*/
			margin-bottom:5px;
		}
		
		div.flow-bar{
			height:30px;
			width:100%;
		}
   </style>
  
</head>
<body>
<div class="toolbar toolbar-auto">
		<table class="query-table">
			<tr>
				<th>
					入库号:
				</th>
				<td>
					<input type="text" name="inNumber"/>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" >查询</button>
					<?php if($hasEditPermission){ ?>
					<button class="add-btn btn">添加入库单</button>
					<?php } ?>
				</td>
			</tr>						
		</table>
	</div>
	<div class="flow-bar">
			<center  class="flow-table">
					<div class="clear:both;"></div>	
		  			<div class="flow-node active  total" status="">全部<span class="count"></span></div>
		  			<div class="flow-split">&nbsp; &nbsp; &nbsp; </div>
		 			<div class="flow-node disabled" status="0">编辑中<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="11">打印标签<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="12">装箱<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="14">FBA计划<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="15">出库<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="20">待发货<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="30">已发货<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="70">入库完成<span class="count"></span></div>
	 		</center>
	</div>
	<div class="clear:both;"></div>	
	<div id="details_tab"></div>
	<div class="grid-content" id="tab-content"></div>
</body>
</html>
