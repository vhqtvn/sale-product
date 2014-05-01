<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		include_once ('config/config.php');
  		include_once ('config/header.php');
  		
		echo $this->Html->script('modules/product/developer/dev_list');
		
		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$security  = ClassRegistry::init("Security") ;
		
		$user = $this->Session->read("product.sale.user") ;
		$loginId = $user["LOGIN_ID"] ;
		
		$PDT_CREATE = $security->hasPermission($loginId , 'PDT_CREATE') ;
		$PDT_UPDATE = $security->hasPermission($loginId , 'PDT_UPDATE') ;
		
		$COST_EDIT_PROFIT   						= $security->hasPermission($loginId , 'COST_EDIT_PROFIT') ;
		$COST_VIEW_PROFIT  						= $security->hasPermission($loginId , 'COST_VIEW_PROFIT') ||$COST_EDIT_PROFIT  ;
	?>
  
   <script type="text/javascript">
    var $PDT_CREATE = <?php echo $PDT_CREATE?'true':'false' ;?> ;
	var $PDT_UPDATE = <?php echo $PDT_UPDATE?'true':'false' ;?> ;
	var $COST_VIEW_PROFIT  = <?php echo $COST_VIEW_PROFIT?'true':'false' ;?> ;
	var $loginId = '<?php echo $loginId;?>' ;

	   $(function(){
		   DynTag.listByType("productDevTag",function(entityType,tagId){
		    	 $(".grid-content-details").llygrid("reload",{tagId:tagId},true) ;
			}) ;
		}) ;
   </script>
	
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
		}
		
		div.flow-split{
			float:left ;
			/*display:none;*/
			margin-bottom:5px;
		}
		
		div.flow-bar{
			height:50px;
		}
   </style>

</head>
<body>
  <div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="开发列表" style="padding:2px;">
	<div class="toolbar toolbar-auto toolbar1 query-container">
		<table>
			<tr>
				<th>
					关键字:
				</th>
				<td>
					<input type="text" id="searchKey" placeHolder="输入ASIN、产品名称、开发标题" style="width:400px;"/>
				</td>
				<td>
					<select  id="devStatus" style="width:80px;"> 
						<option value="">--全部--</option>
						<option value="2">跟卖</option>
						<option value="1">自有</option>
					</select>
				</td>								
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary" data-widget="grid-query"  data-options="{gc:'.grid-content-details',qc:'.toolbar1'}">查询</button>
				</td>
				<td class="toolbar-btns">
					<button class="query-btn btn btn-primary  create-product-dev" >开发新产品</button>
				</td>
			</tr>						
		</table>
	</div>	
	<div class="flow-bar">
			<center  class="flow-table">
					<div class="clear:both;"></div>	
		  			<div class="flow-node active  total" status="">全部<span class="count"></span></div>
		  			<div class="flow-split">&nbsp; &nbsp; &nbsp; </div>
		 			<div class="flow-node disabled" status="10">产品分析<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="20">产品询价<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="25">成本利润<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="30">产品经理审批<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="40">总监审批<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="41">样品下单<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="42">样品达到<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="43">产品资料准备<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="44">样品检测<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="45">检测审批<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="46">上传资料准备<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="50">录入货品<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="60">制作Listing<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="70">Listing审批<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="72">试销采购<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="76">营销展开<span class="count"></span></div>
					<div class="flow-split">-</div>
					<div class="flow-node disabled" status="80">结束<span class="count"></span></div>		
					<div class="clear:both;"></div>	
	 		</center>
	</div>
	<div class="grid-content-details" style="width:99.5%;margin-top:10px;">
	</div>
	</div>
	<div region="west"  split="true" border="true" title="分类" style="width:170px;">
			<div id="tree-wrap" style="height:300px;overflow:auto;">
					<div id="default-tree" class="tree" style="padding: 5px; "></div>
			</div>
			<div id="tag-container"  class="tag-list-container"  style="position:relative;width:auto;">
				<h4>标签</h4>
				<ul style="width:auto;margin-top:5px;"></ul>
			</div>
	</div>
</body>
</html>
