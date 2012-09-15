<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>供应商</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('tree/jquery.tree');
		
		$id = "" ;
		$name = '' ;
		$address = '' ;
		$contactor  ="" ;
		$phone = '' ;
		$mobile = '' ;
		$fax = '' ;
		$zip_code = '' ;
		$url = "" ;
		$email ="" ;
		$qq ="" ;
		$products = "" ;
		
		 if( $supplier !=null){
		 	$id =$supplier[0]['sc_supplier']["ID"] ;
			$name =$supplier[0]['sc_supplier']["NAME"] ;
			$address =$supplier[0]['sc_supplier']["ADDRESS"] ;
			$contactor =$supplier[0]['sc_supplier']["CONTACTOR"] ;
			$phone =$supplier[0]['sc_supplier']["PHONE"] ;
			$mobile =$supplier[0]['sc_supplier']["MOBILE"] ;
			$fax =$supplier[0]['sc_supplier']["FAX"] ;
			$zip_code =$supplier[0]['sc_supplier']["ZIP_CODE"] ;
			$url =$supplier[0]['sc_supplier']["URL"] ;
			$email  =$supplier[0]['sc_supplier']["EMAIL"] ;
			$qq  =$supplier[0]['sc_supplier']["QQ"] ;
			$products  =$supplier[0]['sc_supplier']["PRODUCTS"] ;
		 }
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
   </style>

   <script>
   		 var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
	    var treeMap  = {} ;
	    var asin = '' ;
	
	    <?php
	    	$ss = explode(",",$products) ;
	    
	    	$index = 0 ;
			foreach( $categorys as $Record ){
				$sfs = $Record['sc_product_category']  ;
				$id1   = $sfs['ID'] ;
				$name1 = $sfs['NAME'] ;
				$pid  = $sfs['PARENT_ID'] ;
				echo " var item$index = {id:'$id1',text:'$name1',memo:'".$sfs['MEMO']."',isExpand:true,disabled:1} ;" ;
				
				foreach($ss as $s){
					if( $s  == $id1 ){
						echo " item$index ['checkstate'] = 1 ;" ;
					}
				}
				
			
				echo " treeMap['id_$id1'] = item$index  ;" ;
				if(empty($pid)){
					echo " item$index ['childNodes'] = item$index ['childNodes']||[] ;" ;
					echo "treeData.childNodes.push( item$index ) ;" ;
				}else{
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
				cascadeCheck:false,
           }) ;
           
			
			$("button").click(function(){
				
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/supplier/saveSupplier",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.location.reload() ;
							window.close() ;
						}
					}); 
				};
			})
		})
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">
<input type="hidden" id="id" value="<?php echo $id;?>"/>
	<table class="table table-bordered">
		<tr>
			<td style="width:100px;">供应商名称：</td><td><?php echo $name;?></td>
			<td rowspan=11 style="width:100px;vertical-align:top;">
				<div id="default-tree" class="tree" style="padding: 5px; "></div>
			</td>
		</tr>
		<tr>
			<td>供应商地址：</td><td><?php echo $address;?></td>
		</tr>
		<tr>
			<td>联系人：</td><td><?php echo $contactor;?></td>
		</tr>
		<tr>
			<td>联系电话：</td><td><?php echo $phone;?></td>
		</tr>
		<tr>
			<td>手机：</td><td><?php echo $mobile;?></td>
		</tr>
		<tr>
			<td>传真：</td><td><?php echo $fax;?></td>
		</tr>
		<tr>
			<td>QQ/MSN/Skype：</td><td><?php echo $qq;?></td>
		</tr>
		<tr>
			<td>Email：</td><td><?php echo $email;?></td>
		</tr>
		<tr>
			<td>邮编：</td><td><?php echo $zip_code;?></td>
		</tr>
		<tr>
			<td>网址：</td><td><?php echo $url;?></td>
		</tr>
		<tr>
			<td>备注：</td><td><?php echo $supplier[0]['sc_supplier']["MEMO"];?></td>
		</tr>
	</table>
</form>
</html>