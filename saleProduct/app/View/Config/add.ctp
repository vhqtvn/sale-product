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
		echo $this->Html->css('style-all');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('../grid/query');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('../grid/grid');
		echo $this->Html->script('validator/jquery.validation');
		
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
						url:"/saleProduct/index.php/config/saveConfigItem",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.$(".grid-content").llygrid("reload") ;
							window.close() ;
						},error:function(){
							alert("操作出现异常！") ;
						}
					}); 
				};
				return false ;
			}) ;
			
			$(".cost").keyup(function(){
				calcTotalCost() ;
			}) ;
			$(".cost").blur(function(){
				calcTotalCost() ;
			}) ;
			
			calcTotalCost() ;
			
		})
		
		function calcTotalCost(){
			var totalCost = 0 ;
				$(".cost").each(function(){
					totalCost = totalCost + parseFloat($(this).val()||0) ;
				}) ;
				$("#TOTAL_COST").val(totalCost.toFixed(2)) ;
		}
   </script>
</head>
<body>
<form id="personForm" action="#" data-widget="validator,ajaxform">

<input type="hidden" id="id" value="<?php echo $configItem[0]["sc_config"]["ID"];?>"/>

<fieldset>
	<legend>配置项</legend>
	<table>
		<tr>
			<td>类型：</td><td>
				<select id="type" data-validator='required'>
					<option value=""></option>
					<option value="strategy" selected>策略</option>
					<?php
					if(  $loginId == 'manage'){//总经理
					echo '
					<option value="relation">关系</option>
					<option value="field" >字段</option>
					' ;
					}
					 ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>值（英文）：</td><td><input class="cost"  data-validator='required'  type="text" id="key" value="<?php echo $configItem[0]["sc_config"]["KEY"];?>"/></td>
		</tr>
		<tr>
			<td>显示名称：</td><td><input class="cost"  data-validator='required'  type="text" id="label" value="<?php echo $configItem[0]["sc_config"]["LABEL"];?>"/></td>
		</tr>
		<tr>
			<td>备注：</td><td><textarea class="cost span4" id="memo" style="height:100px;"><?php echo $configItem[0]["sc_config"]["MEMO"];?></textarea></td>
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