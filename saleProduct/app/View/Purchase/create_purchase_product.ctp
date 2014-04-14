<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>采购任务产品</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   		
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/dialog/jquery.dialog');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/listselectdialog/jquery.listselectdialog');
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('tab/jquery.ui.tabs');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('listselectdialog/jquery.listselectdialog');
		echo $this->Html->script('modules/purchase/create_purchase_product');
		echo $this->Html->script('dialog/jquery.dialog');
		echo $this->Html->script('calendar/WdatePicker');
		
		$Sale  = ClassRegistry::init("Sale") ;
		$security  = ClassRegistry::init("Security") ;
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
		$Supplier  = ClassRegistry::init("Supplier") ;
		
	?>
  
<style>
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		#details_tab.ui-corner-all{
			border:none;
   		}
   		
   		.nav-tabs ul li span{
			margin-top:5px!important;
   			display:block;
   		}
   </style>
</head>


<body class="container-popup">
	<div  class="flow-bar">
		<center>
			<table class="flow-table">
				
			</table>
			<div class="flow-action">
			</div>
		</center>
	</div>
	<!-- apply 主场景 -->
	<div class="apply-page">
		<div class="container-fluid">
		
			<div id="details_tab"></div>
			<div id="base-info">
		        <form id="personForm" action="#" data-widget="validator,ajaxform" class="form-horizontal" >
					<!-- panel 头部内容  此场景下是隐藏的-->
					<div class="panel apply-panel">
						<!-- panel 中间内容-->
						<div class="panel-content">
							<!-- 数据列表样式 -->
							<table class="form-table" >
								<caption>基本信息</caption>
								<tbody>
									<tr>
										<th>货品：</th>
										<td colspan=3>
											<input type="hidden"   id="realId"  
											value=""/>
											<input type="text"   id="realName"  readonly
													value=""/>
											<button class="btn btn-real-product">选择</button>
										</td>
									</tr>										   
									<tr>
										<th>执行人：</th><td>
											<input type="hidden"   id="executor"  
											value=""/>
											<input type="text"   id="executorName"  readonly
													value=""/>
											<button class="btn btn-charger">选择</button>
										</td>
									</tr>
									<tr>
										<th><button class="btn btn-tags 10-input  20-input 30-input  40-input 45-input input no-disabled">添加</button>标签：</th>
										<td colspan=3>
											<input id="tags"   type="hidden"   value=""/>
											<ul class="tag-container" style="list-style: none;">
											</ul>
										</td>
									</tr>
									<tr>
										<th>采购时限：</th>
										<td colspan=3>
										<input id="startTime" class="10-input input"  data-validator="required"  type="text"  
											value="" data-widget="calendar"/>到
										<input id="endTime"  class="10-input input"   data-validator="required"  type="text" 
											value="" data-widget="calendar"/></td>
									</tr>
									<tr>
										<th>计划采购数量：</th>
										<td><input id="planNum"   class="10-input input"   
													data-validator="required"    type="text" value='' /></td>
									</tr>
									<tr>
										<th>采购限价：</th>
										<td><input id="limitPrice"   class="10-input 20-input 30-input input"   type="text"  
													 value='' /></td>
									</tr>
									<tr>
										<th>备注：</th><td colspan=3>
										<textarea class="10-input input" style="width:500px;height:80px;" id="memo"></textarea>
										</td>
									</tr>
								</tbody>
						</table>
						
						</div>
						
						<!-- panel脚部内容-->
	                    <div class="panel-foot">
							<div class="form-actions">
								<button type="button" class="btn btn-primary btn-save">提&nbsp;交</button>
							</div>
						</div>
						
					</div>
				</form>
			</div>
		</div>
	</div>
</body>

</html>