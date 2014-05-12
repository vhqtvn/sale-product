<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
    <?php
   		include_once ('config/config.php');
  		include_once ('config/header.php');
		echo $this->Html->script('modules/account/product_lists');

		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$user = $this->Session->read("product.sale.user") ;
		$group=  $user["GROUP_CODE"] ;
	?>
	
    <script type="text/javascript">

   var accountId = '<?php echo $accountId ;?>' ;
   var currentAccountId = accountId ;
   $(function(){
	   DynTag.listByType("listingTag",function(entityType,tagId){
	    	 $(".grid-content").llygrid("reload",{tagId:tagId},true) ;
		}) ;
	}) ;
   </script>
   
   <style style="text/css">
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.rights-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:red;
   			display:block;
   			float:left;
   		}
   		
   		.ranking-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#800000;
   			display:block;
   			float:left;
   		}
   		
   		.country-area-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#0000FF;
   			display:block;
   			float:left;
   		}
   		
   		.lly-grid-cell-input{
   		}
   		
   		.query-bar ul{
   			display:block;
   			margin_bottom:5px;
   			height:auto;
   			width:100%;
   		}
   		
   		.query-bar ul li{
   			list-style-type:none;
   			float:left;
   			padding:3px 0px;
   		}
   		
   		.query-bar ul li label{
   			float:left;
   			margin:0px 0px;
   			margin-left:15px;
   		}
   		
   		.query-bar{
   			clear:both;
   		}
   		
   		li select,li input{
   			width:auto;
   			padding:0px;
   		}
   		
   		.popover-inner  .popover-title{
			font-size:12px;
   		}
   </style>

</head>
<body style="magin:0px;padding:0px;">
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto query-bar">
				<table style="width:100%;" class="query-table">	
					<tr>
						
						<th>关键字:</th>
						<td colspan="2">
							<input type="text" name="searchKey" placeHolder="ASIN、SKU、名称"  style="width:400px"/>
						
							<button class="btn btn-primary query query-btn" >查询</button>
							<!--
							<button class="btn btn-primary btn-mini query-reply-btn">重复产品过滤</button>
				 			<button class="btn btn-primary btn-mini product-category-btn">编辑分类产品</button>
				 			  -->
						</td>
					</tr>						
				</table>
			</div>
			
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
			
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="营销产品分类" style="width:200px;">
			<div id="tree-wrap">
			<div id="default-tree_0" class="tree" style="padding: 5px; "></div>
			</div>
		</div>
   </div>
	
</body>
</html>
