<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>功能列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('grid/query');
		echo $this->Html->script('modules/home/widgets');
		//test tree
		$Utils  = ClassRegistry::init("Utils") ;
		//debug($result) ;
	?>

</head>
<body>
	<div class="home-page">	
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span6 first">
					<div class="panel">
						<div class="panel-head">
							<div class="row-fluid">
								<div class="span6 first">							
									<h2>标签动态</h2>
								</div>
								<div class="span6">
								</div>
							</div>
							<a href="#" class="toggle"></a>
						</div>
						<div class="panel-content  tag-dyn">
								
						</div>
					</div>
				
				
					<!-- panel内容开始 -->
					<div class="panel">
						<div class="panel-head">
							<div class="row-fluid">
								<div class="span6 first">							
									<h2>产品开发任务</h2>
								</div>
								<div class="span6">
								</div>
							</div>
							<a href="#" class="toggle"></a>
						</div>
						<div class="panel-content">
							<table class="table table-striped table-condensed">
								<tbody>
									<tr>
										<th>我的待审批的开发任务(产品专员)</th>
										<td class="cpzyMy-product">加载中...</td>
									</tr>
									<tr>
										<th>所有待审批的开发任务(产品专员)</th>
										<td class="cpzyAll-product">加载中...</td>
									</tr>
									<tr>
										<th>待审批的开发任务(产品经理)</th>
										<td class="cpjl-product">加载中...</td>
									</tr>
									<tr>
										<th>待审批的开发任务(总经理)</th>
										<td class="zjl-product">加载中...</td>
									</tr>
								</tbody>
							</table>								
						</div>
					</div>
					
					
					
					<!-- panel 内容结束 -->

					<!-- panel内容开始 -->
					<div class="panel">
						<div class="panel-head">
							<div class="row-fluid">
								<div class="span6 first">							
									<h2>采购任务待办</h2>
								</div>
								<div class="span6">
                                	<div class="pull-right">
                                    </div>
								</div>
							</div>
							<a href="#" class="toggle"></a>
						</div>
						<div class="panel-content">
							<!-- 数据列表默认样式 start -->						
							<table class="table table-striped table-condensed">
								<tbody>
									<tr>
										<th>我的采购计划</th>
										<td class="my-purchase">加载中...</td>
									</tr>
									<tr>
										<th>我的采购执行计划</th>
										<td class="myexecutor-purchase">加载中...</td>
									</tr>
									<tr>
										<th>所有采购计划</th>
										<td class="all-purchase">加载中...</td>
									</tr>
								</tbody>
							</table>
							<!-- 数据列表默认样式 end -->
									
						</div>
					</div>
					<!-- panel 内容结束 -->
					
				</div>
				<div class="span6">
					<!-- panel内容开始 -->
					<div class="panel">
						<div class="panel-head">
							<div class="row-fluid">
								<div class="span6 first">							
									<h2>订单信息</h2>
								</div>
								<div class="span6">
                                	<div class="pull-right">
                                    </div>
								</div>
							</div>
							<a href="#" class="toggle"></a>
						</div>
						<div class="panel-content">
							<table class="table table-striped table-condensed">
								<tbody>
									<tr>
										<th>未审核单品订单</th>
										<td class="orderOne-order">加载中...</td>
									</tr>
									<tr>
										<th>未审核多品订单</th>
										<td class="orderMany-order">加载中...</td>
									</tr>
									<tr>
										<th>我的处理中拣货单</th>
										<td class="pickedMy-order">加载中...</td>
									</tr>
									<tr>
										<th>所有处理中拣货单</th>
										<td class="pickedAll-order">加载中...</td>
									</tr>
								</tbody>
							</table>		
						</div>
					</div>
					<!-- panel 内容结束 -->
                    
                    
                    <!-- panel内容开始 -->
					<div class="panel ">
						<div class="panel-head">
							<div class="row-fluid">
								<div class="span6 first">							
									<h2>入库计划</h2>
								</div>
								<div class="span6">
                                	<div class="pull-right">
                                    </div>
								</div>
							</div>
							<a href="#" class="toggle"></a>
						</div>
						<div class="panel-content">
							<table class="table table-striped table-condensed">
								<tbody>
									<tr>
										<th>编辑中</th>
										<td class="0-inplan inplan">加载中...</td>
									</tr>
									<tr>
										<th>待审批</th>
										<td class="10-inplan inplan">加载中...</td>
									</tr>
									<tr>
										<th>待收货</th>
										<td class="20-inplan inplan">加载中...</td>
									</tr>
									<tr>
										<th>已发货</th>
										<td class="30-inplan inplan">加载中...</td>
									</tr>
									<tr>
										<th>到达海关</th>
										<td class="40-inplan inplan">加载中...</td>
									</tr>
									<tr>
										<th>验货中</th>
										<td class="50-inplan inplan">加载中...</td>
									</tr>
									<tr>
										<th>入库中</th>
										<td class="60-inplan inplan">加载中...</td>
									</tr>
								</tbody>
							</table>	
						</div>
					</div>
					<!-- panel 内容结束 -->
                 
				</div>
			</div>
		</div>		
	</div> 
</body>
</html>
