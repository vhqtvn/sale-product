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
		echo $this->Html->css('../js/tab/jquery.ui.tabs');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('tree/jquery.tree');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
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
		$memo = '' ;
		
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
			$memo = $supplier[0]['sc_supplier']["MEMO"]; 
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
		
		.table-bordered tr td,.table-bordered tr th{
			padding:5px;
		}
		.WdateDiv, .ui-corner-all {
			border:none;
		}
		
		.eva-ul{
			list-style: none;
		}
		
		.eva-ul li{
			float:left;
			width:350px;
			padding:5px;
			margin:5px;
			border:1px solid #CCC;
		}
		
		.eva-ul li textarea{
			margin:5px 2px;
		}
   </style>

   <script>
		var asin = '<?php echo $asin;?>' ;
   
   	    var treeData = {id:"root",text:"产品分类",isExpand:true,childNodes:[]} ;
	    var treeMap  = {} ;
	  
	    <?php
	    	$ss = explode(",",$products) ;
	    	$Utils  = ClassRegistry::init("Utils") ;
	    	
	    	$Utils->echoTreeScript( $categorys ,$ss, function( $item, $index ,$ss ){
			    	$id__ = $item ['ID'];
					$name__ = $item ['NAME'];
					echo " var item$index = {id:'$id__',text:'$name__',memo:'" . $item ['MEMO'] . "',isExpand:true} ;";
					
					foreach ( $ss as $s ) {
						if ($s == $id__) {
							echo " item$index ['checkstate'] = 1 ;";
						}
					}
	    	} ) ;
		?>
   
   
		$(function(){
			$('#default-tree').tree({//tree为容器ID
				source:'array',
				data:treeData ,
				showCheck:true,
				cascadeCheck:false
           }) ;

			var tab = $('#tabs-default').tabs( {//$this->layout="index";
				tabs:[
					{label:'基本信息',content:"base-info"}
					,{label:'评价',content:"evaluate"}
					,{label:'供应产品',content:"supllie-product"}
				] ,
				height:'588x'
			} ) ;

			$(".commit").click(function(){

				if(window.confirm("确认保存？")){
					if( !$.validation.validate('#personForm').errorInfo ) {
						var json = $("#personForm").toJson() ;
						var vals = $('#default-tree').tree().getSelectedIds()  ;
						
						json.products = vals.join(",") ;

						$.dataservice("model:Supplier.saveSupplier",json,function(result){
							jQuery.dialogReturnValue(result) ;
							window.close();
						}) ;
						/*
						$.ajax({
							type:"post",
							url:contextPath+"/supplier/saveSupplier/"+asin,
							data:json,
							cache:false,
							dataType:"json",
							success:function(result,status,xhr){
								jQuery.dialogReturnValue(result) ;
								window.close();
							}
						}); 
						*/
					};
				}
			})
		})
   </script>

</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">

<div id="tabs-default" ></div>
	<div id="base-info">
			<input type="hidden" id="id" value="<?php echo $id;?>"/>
			<table class="table table-bordered">
				<caption>供应商信息</caption>
				<tr>
					<th>供应商名称：</th>
					<td colspan="3"><input  data-validator="required" type="text" id="name" value="<?php echo $name;?>"/></td>
				</tr>
				<tr>
					<th>供应商地址：</th><td colspan="3"><input  data-validator="required" type="text" id="address" value="<?php echo $address;?>"/></td>
				</tr>
				<tr>
					<th>联系人：</th><td><input type="text" id="contactor" value="<?php echo $contactor;?>"/></td>
					<th>联系电话：</th><td><input type="text" id="phone" value="<?php echo $phone;?>"/></td>
				</tr>
				<tr>
					<th>手机：</th><td><input type="text" id="mobile" value="<?php echo $mobile;?>"/></td>
					<th>传真：</th><td><input type="text" id="fax" value="<?php echo $fax;?>"/></td>
				</tr>
				<tr>
					<th>QQ/MSN/Skype：</th><td><input type="text" id="qq" value="<?php echo $qq;?>"/></td>
					<th>Email：</th><td><input type="text" id="email" value="<?php echo $email;?>"/></td>
				</tr>
				<tr>
					<th>邮编：</th><td colspan="3"><input type="text" id="zip_code" value="<?php echo $zip_code;?>"/></td>
				</tr>
				<tr>
					<th>网址：</th><td colspan="3"><input type="text" id="url" value="<?php echo $url;?>"/></td>
				</tr>
				<tr>
					<th>备注：</th><td colspan="3"><textarea id="memo" style="width:300px;height:80px;"><?php echo $memo;?></textarea></td>
				</tr>
			</table>
	</div>
	<div id="supllie-product">
		<div id="default-tree" class="tree" style="padding: 5px;overflow-y:auto;overflow-x:hidden;height:460px; "></div>
	</div>
	
	<div id="evaluate">
		<div class="toolbar toolbar-auto">
				<table style="width:100%;" class="query-table  save-sale-price">
					<tr>
						<th>总体评价:</th>
						<td>
								<select name="evaluate">
										<option value="">--选择评价--</option>
										<option value="4">优先推荐</option>
										<option value="3">推荐</option>
										<option value="2">备选</option>
										<option value="1">不推荐</option>
								</select>
						</td>
					</tr>							
				</table>	
			</div>
			<ul class="eva-ul">
	<?php 
	$SqlUtils  = ClassRegistry::init("SqlUtils") ;
	$metas = $SqlUtils->exeSqlWithFormat("select * from sc_supplier_evaluate_meta",array()) ;
	$suppleEva = $SqlUtils->exeSqlWithFormat("select * from sc_supplier_evaluate where supplier_id = '{@#id#}' ",array('id'=>$id)) ;
	
	foreach($metas as $meta){
		$selected = "" ;
		if(!empty($suppleEva)){
			foreach($suppleEva as $se){
				if( $se['META_CODE'] ==$meta['CODE']  ){
					$selected = $se['SCORE'] ;
				}
			}
		}
		
		
		?>
		<li>
			<?php echo $meta['NAME']?>：
			<select id="<?php echo $meta['CODE']?>_select"></select>
				<script>
					var options = <?php echo $meta['OPTIONS'];?> ;
					var selectId = '<?php echo $meta['CODE']?>_select' ;
					$("#"+selectId).empty() ;
					$("#"+selectId).append("<option value=''>选择评价</option>")
					for(var o in options){
						var _s = "" ;
						if( o == '<?php echo $selected;?>'  ) _s ="selected" ;
						$("#"+selectId).append("<option value='"+o+"' "+_s+">"+options[o]+"</option>") ;
					}
				</script>
				
				<textarea id="<?php echo $meta['CODE']?>_memo" style="width:85%;;height:60px;"></textarea>
		</li>
<?php 	}
	
	?>
	</ul>
	</div>
					<div class="panel-foot" style="position:fixed;bottom:0px;left:0px;right:0px;">
						<div class="form-actions col2">
							<button  type="submit" class="btn btn-primary commit">提&nbsp;交</button>
							
							<button onclick="window.close();" class="btn">关&nbsp;闭</button>
						</div>
					</div>
</form>
</body>
</html>