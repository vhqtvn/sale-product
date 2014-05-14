<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/layout/jquery.layout');
		echo $this->Html->css('../js/tree/jquery.tree');
		
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('layout/jquery.layout');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('modules/keyword/nicheList');
		
	?>
	
   <style>
   		*{
   			font:12px "微软雅黑";
   		}
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
		}
		
		div.flow-split{
			float:left ;
			/*display:none;*/
			margin-top:-10px;
		}
		
		div.flow-bar{
			height:25px;
			margin:5px auto;
		}
		
		.count{
			font-weight:bold;
			color:red;
		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="开发列表" style="padding:2px;">
					<div class="toolbar task-t toolbar-auto">
						<table>
							<tr>
								<th>关键字名称：
								</th>
								<td>
									<input type="text" name="name" class="input-medium"/>
								</td>	
								<th>开发责任人：
								</th>
								<td>
									<input type="text" name="dev_charger_name" class="input-medium"/>
								</td>					
								<td class="toolbar-btns" rowspan="3">
									<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.niche-grid',qc:'.task-t'}">查询</button>
								</td>
							</tr>						
						</table>
				</div>
				<center  class="flow-table">
					<div class="flow-bar">
									<div class="clear:both;"></div>	
						  			<div class="flow-node active  total" status="">全部<span class="count"></span></div>
						  			<div class="flow-split">&nbsp; &nbsp; &nbsp; </div>
						 			<div class="flow-node disabled" status="10">Niche分析<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node disabled" status="20">Niche审批<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node disabled" status="30">产品开发中<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node disabled" status="40">开发结束<span class="count"></span></div>
					</div>
					</center>
				<div class="niche-grid" ></div>
	</div>
	<div region="west"  split="true" border="true" title="分类" style="width:170px;">
			<div id="tree-wrap" >
					<div id="default-tree" class="tree" style="padding: 5px; "></div>
			</div>
	</div>


		
		
</body>
</html>
