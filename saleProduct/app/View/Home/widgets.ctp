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
	
		<style type="">
   		div.flow-node{
			cursor: pointer ;
			/*width:60px;
   			word-wrap: break-word;
   			height:35px;*/
   		}
   		
		div.flow-node {
			
   			float:left ;
			margin-bottom:5px;   		
		}
		
		div.flow-split{
			float:left ;
			/*display:none;*/
			margin-bottom:5px;
		}
		
		div.flow-bar{
			height:50px;
		}
		
		.flow-node{
			border: 1px solid #CCC;
			padding:1px 2px;
			background:#EEE;
		}
		
		.has-count{
			color:red;
			font-weight:bold;
		}
   </style>
 </head>
<body>
	<div class="home-page">	
		<div class="container-fluid">
			<div class="row-fluid">
					<div class="span12">
						<div class="panel  purchase-widget">
								<div class="panel-head">
									<div class="row-fluid">
										<div class="span6 first">							
											<h2>采购单</h2>
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
									<table class="flow-table">						
									<tbody>
										<tr>	
										<td><div class="flow-node  normal" status="45,46">待采购<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node normal" status="47">采购审批<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node normal" status="48">待交易<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node normal" status="49">待收货<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node normal" status="50">QC验货<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node normal" status="60">货品入库<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node normal" status="75">发货FBA<span class="count"></span></div></td>
										
										<td class="flow-split">&nbsp;&nbsp;</td><td><div class="flow-node audit">采购审计<span class="audit-count"></span></div></td>
										<td class="flow-split">&nbsp;&nbsp;</td><td><div class="flow-node  repair-node" status="1">未设置执行用户<span class="count"></span></div></td>
										<td class="flow-split">-</td><td><div class="flow-node repair-node" status="2">未设置限价<span class="count"></span></div></td>
										</tr>					
										</tbody>
									</table>	
									<!-- 数据列表默认样式 end -->
											
								</div>
							</div>
					</div>
			</div>
			<div class="row-fluid">
				<div class="span12  first">
						<div class="panel  productDev-widget">
								<div class="panel-head">
									<div class="row-fluid">
										<div class="span6 first">							
											<h2>产品开发</h2>
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
						 			<div class="flow-node " status="10">产品分析<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="20">产品询价<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="25">成本利润<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="30">产品经理审批<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="40">总监审批<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="41">样品下单<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="42">样品达到<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="43">产品资料准备<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="44">样品检测<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="45">检测审批<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="46">上传资料准备<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="50">录入货品<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="60">制作Listing<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="70">Listing审批<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="72">试销采购<span class="count"></span></div>
									<div class="flow-split">-</div>
									<div class="flow-node " status="76">营销展开<span class="count"></span></div>
								</div>
							</div>
				
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12  first">
						<div class="panel  productInfoComplete-widget">
								<div class="panel-head">
									<div class="row-fluid">
										<div class="span6 first">							
											<h2>产品数据补充</h2>
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
						 			<table class="flow-table">						
									<tbody>
									<tr>	
									<!-- 
									<td><div class="flow-node  " status="7">Listing未关联货品<span class="count"></span></div></td>
									<td class="flow-split">-</td><td>
									 -->
									<div class="flow-node  " status="1">采购成本不完整<span class="count"></span></div></td>
									<td class="flow-split">-</td><td><div class="flow-node " status="3">Amazon费用缺失<span class="count"></span></div></td>
									<td class="flow-split">-</td><td><div class="flow-node " status="4">重量缺失<span class="count"></span></div></td>
									<td class="flow-split">-</td><td><div class="flow-node " status="6">报关数据缺失<span class="count"></span></div></td>
									<td class="flow-split">-</td><td><div class="flow-node " status="7">产品属性为空<span class="count"></span></div></td>
									</tr>					
									</tbody>
									</table>	
								</div>
							</div>
				
				</div>
			</div>
		</div>		
	</div> 
</body>
</html>
