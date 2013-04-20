<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
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
		echo $this->Html->script('modules/account/product_lists_price');
		
		$SqlUtils  = ClassRegistry::init("SqlUtils") ;
	?>
	
   <script type="text/javascript">
	var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
    var treeMap  = {} ;

    <?php
    $Utils  = ClassRegistry::init("Utils") ;
    
    $Utils->echoTreeScript( $categorys ,null, function( $sfs, $index ,$ss ){
    	$id   = $sfs['ID'] ;
			$name = $sfs['NAME']."(".$sfs['TOTAL'].")" ;
			$pid  = $sfs['PARENT_ID'] ;
    	echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;\n" ;
    } ) ;
    
    echo " treeMap['id_uncategory'] = {id:'uncategory',text:'未分类产品',memo:'',isExpand:true} ;\n" ;
    echo " treeData.childNodes.push( treeMap['id_uncategory']  ) ;\n" ;
    
	?>
   
   var accountId = '<?php echo $accountId ;?>' ;

	var currentAccountId = accountId ;
	var currentCategoryId = "" ;
	var editImg = '<?php echo $this->Html->image('example.gif',array("title"=>"修改")) ?>' ;
   </script>
   
   <style>
   		*{
   			font:12px "微软雅黑";
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
   </style>

</head>
<body style="magin:0px;padding:0px;">
	<div data-widget="layout" style="width:100%;height:100%;">
		<div region="center" split="true" border="true" title="产品列表" style="padding:2px;">
			<div class="toolbar toolbar-auto query-bar">
				<table style="width:100%;" class="query-table">	
					<tr>
						<th>ASIN:</th>
						<td>
							<input type="text" name="asin" style="width:100px"/>
						</td>
						<th>名称:</th>
						<td>
							<input type="text" name="title" style="width:100px"/>
						</td>
						<th>价格:</th>
						<td>
							从<input type="text" name="price1" style="width:50px"/>到<input type="text" name="price2" style="width:50px"/>
						</td>
						<th>销售渠道:</th>
						<td>
							<select name='fulfillmentChannel'  class="span2">
								<option value=''>全部</option>
								<option value='AMAZON_NA'>Amazon</option>
								<option value='Merchant'>Merchant</option>
								<option value='-'>未知</option>
							</select>
						</td>
					</tr>
					<tr>	
						<th>使用程度:</th>
						<td>
							<select name='itemCondition' class="span2">
								<option value=''>全部</option>
								<option value=11>New</option>
								<option value=1>Used</option>
								<option value='-'>未知</option>
							</select>
						</td>
						<th>FM商品:</th>
						<td>
							<select name='isFM' class="span2">
								<option value=''>全部</option>
								<option value="FM">FM</option>
								<option value="NEW">NEW</option>
							</select>
						</td>
						<th>排名:</th>
						<td>
							<select name='pm' class="span2">
								<option value=''>全部</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="other">其他</option>
							</select>
						</td>
						<td colspan="2">
							<button class="btn btn-primary query query-btn" >查询</button>
							
							<button class="price-update btn btn-primary  btn-danger">更新价格到AMAZON</button>
						</td>
					</tr>						
				</table>
			</div>
		
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>
			
		</div>
		<div region="west" icon="icon-edit" split="true" border="true" title="营销产品分类" style="width:150px;">
			<div id="default-tree" class="tree" style="padding: 5px; "></div>
		</div>
   </div>
	
</body>
</html>
