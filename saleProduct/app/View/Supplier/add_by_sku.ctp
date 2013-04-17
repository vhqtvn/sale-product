<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   		include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('tree/jquery.tree');
?>
   <style>
   		*{
   			font:12px "微软雅黑";
   		}

		.rule-content-item{
			clear:both;
		}

		.item-label,.item-relation,.item-value,.item-value{
			float:left;
		}
		
		.table-bordered tr td,.table-bordered tr th{
			padding:5px;
		}
   </style>

   <script>
		var sku = '<?php echo $sku;?>' ;
   
   	    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
	    var treeMap  = {} ;
	  
	    <?php
	    
	    $SqlUtils  = ClassRegistry::init("SqlUtils") ;
	    
	    $index = 0 ;
	    foreach( $categorys as $Record ){
	    	$sfs = $SqlUtils->formatObject($Record) ;
	    	//debug($sfs) ;
	    
	    	//$sfs = $Record['sc_product_category']  ;
	    
	    	$id   = $sfs['ID'] ;
	    	$name = $sfs['NAME'] ;
	    	echo " var item$index = {id:'$id',text:'$name',memo:'".$sfs['MEMO']."',isExpand:true} ;" ;
	    
	    	echo " treeMap['id_$id'] = item$index  ;" ;
	    	$index++ ;
	    } ;
	    
	    $index = 0 ;
	    foreach( $categorys as $Record ){
	    $sfs = $SqlUtils->formatObject($Record) ;
	    $id   = $sfs['ID'] ;
	    $name = $sfs['NAME'] ;
	    $pid  = $sfs['PARENT_ID'] ;
	    
	    if(empty($pid)){
	    echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
	    echo "treeData.childNodes.push( item$index ) ;" ;
	    }else{
	    echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
	    echo " treeMap['id_$pid'].childNodes = treeMap['id_$pid'].childNodes||[] ;" ;
	    echo " treeMap['id_$pid'].childNodes.push( item$index ) ;" ;
	    }
	    $index++ ;
	    } ;
	    
		?>
   
   
		$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				showCheck:true,
				cascadeCheck:false
           }) ;
			
			var __ = false ;
			$("button.save").click(function(){
				if( __ ) return ;
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
					var vals = $('#default-tree').tree().getSelectedIds()  ;
					json.products = vals.join(",") ;
					json.sku = sku ;
					__ = true ;
					$.dataservice("model:Supplier.saveSupplierBySku",json,function(){
						window.close() ;
					}) ;
				};
			})
		})
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
<input type="hidden" id="id" value=""/>
	<table class="table table-bordered">
		<caption>供应商信息</caption>
		<tr>
			<th>供应商名称：</th>
			<td><input  data-validator="required" type="text" id="name" value=""/></td>
			<td rowspan=11 style="width:140px;vertical-align:top;">
				<div id="default-tree" class="tree" style="padding: 5px;overflow-y:auto;overflow-x:hidden;height:460px; "></div>
			</td>
		</tr>
		<tr>
			<th>供应商地址：</th><td><input  data-validator="required" type="text" id="address" value=""/></td>
		</tr>
		<tr>
			<th>联系人：</th><td><input type="text" id="contactor" value=""/></td>
		</tr>
		<tr>
			<th>联系电话：</th><td><input type="text" id="phone" value=""/></td>
		</tr>
		<tr>
			<th>手机：</th><td><input type="text" id="mobile" value=""/></td>
		</tr>
		<tr>
			<th>传真：</th><td><input type="text" id="fax" value=""/></td>
		</tr>
		<tr>
			<th>QQ/MSN/Skype：</th><td><input type="text" id="qq" value=""/></td>
		</tr>
		<tr>
			<th>Email：</th><td><input type="text" id="email" value=""/></td>
		</tr>
		<tr>
			<th>邮编：</th><td><input type="text" id="zip_code" value=""/></td>
		</tr>
		<tr>
			<th>网址：</th><td><input type="text" id="url" value=""/></td>
		</tr>
		<tr>
			<th>备注：</th><td><textarea id="memo" style="width:300px;height:80px;"></textarea></td>
		</tr>
	</table>
	
					<div class="panel-foot">
						<div class="form-actions col2">
							<button  type="buttn" class=" save btn btn-primary">提&nbsp;交</button>
							
							<button onclick="window.close();" class="btn">关&nbsp;闭</button>
						</div>
					</div>
</form>
</html>