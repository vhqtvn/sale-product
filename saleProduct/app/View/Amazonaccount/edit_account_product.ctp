<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title>成本编辑</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../grid/redmond/ui');
		echo $this->Html->css('../grid/grid');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('../js/combotree/jquery.combotree');
		echo $this->Html->css('../js/tree/jquery.tree');
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('validator/jquery.validation');
		echo $this->Html->script('combotree/jquery.combotree');
		echo $this->Html->script('tree/jquery.tree');	
		
		$userId  = $_COOKIE["userId"] ; 
		App::import('Model', 'User') ;
		$u = new User() ;
		$user1 = $u->queryUserByUserName($userId) ;
		$user = $user1[0]['sc_user'] ;

		$loginId = $user["GROUP_CODE"] ;//transfer_specialist cashier purchasing_officer general_manager product_specialist
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
		
		fieldset legend {
			margin-bottom:5px;
		}
		fieldset {
			margin-bottom:8px;
		}
   </style>

   <script>
   		var groupCode = '<?php echo $loginId;?>'
   
		$(function(){

			
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/amazonaccount/saveAccountProduct",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.$(".grid-content").llygrid("reload",{}) ;
							window.close() ;
						},error:function(){
							alert("操作出现异常！") ;
						}
					}); 
				};
				return false ;
			}) ;
			
			
			$("#STRATEGY").change(function(){
				var val = $(this).val() ;
				if(val){
					$("#EXEC_PRICE").attr("disabled",true);
				}else{
					$("#EXEC_PRICE").removeAttr("disabled");
				}
			}) ;
			
		})
		
   </script>
</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">

<input type="hidden" id="ID" value="<?php echo $accountProduct[0]["sc_amazon_account_product"]["ID"];?>"/>

<fieldset>
	<legend>产品价格定制</legend>
	<table class="table">
		<tr>
			<td>ASIN：</td><td><?php echo $accountProduct[0]['sc_product']["ASIN"];?></td>
		</tr>
		<tr>
			<td>TITLE：</td><td><?php echo $accountProduct[0]["sc_product"]["TITLE"];?></td>
		</tr>
		<tr>
			<td>售价策略：</td><td>
				<select id="STRATEGY">
					<option value="">无策略</option>
					<?php foreach( $strategy as $strat ){
						$val = $strat['sc_amazon_config']['NAME'] ;
						$label = $strat['sc_amazon_config']['LABEL'] ;
						echo "<option value='$val'>$label</option>" ;
					} ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>执行价格：</td><td><input class="cost"  data-validator='required'  type="text" id="EXEC_PRICE" value="<?php echo $accountProduct[0]["sc_amazon_account_product"]["EXEC_PRICE"];?>"/></td>
		</tr>
		<tr>
			<td>备注：</td><td><textarea class="cost span4" id="MEMO" style="height:100px;"><?php echo $accountProduct[0]["sc_amazon_account_product"]["MEMO"];?></textarea></td>
		</tr>
	</table>
</fieldset>


	<table>
		<tr>
			<td></td><td><button type="submit" class="btn btn-primary">保存</button></td>
		</tr>
	</table>
</form>
</html>