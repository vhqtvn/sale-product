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
  
  	<?php if($loginId == "purchasing_officer"){//采购专员
	}else if($loginId == "transfer_specialist"){//物流专员

	}else if($loginId == "product_specialist"){//产品专员

	}else if($loginId == "cashier"){//会计

	}else if($loginId == "general_manager" || $loginId == 'manage'){//总经理

	}?>
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
			$(".area").hide() ;
			$("."+groupCode ).show() ;
			
			if(groupCode == "general_manager" || groupCode == 'manage' ){
				$(".area").show() ;
			}else{
				try{
				window.resizeTo(680,300) ;}catch(e){}
			}
			
			if( $("#login_id").val()  ){
				$("#login_id").attr("disabled",true) ;
			}
			
			$("button").click(function(){
				if( !$.validation.validate('#personForm').errorInfo ) {
					var json = $("#personForm").toJson() ;
				
					$.ajax({
						type:"post",
						url:"/saleProduct/index.php/cost/saveCost",
						data:json,
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							window.opener.$(".grid-content-details").llygrid("reload",{asin:"<?php echo $asin;?>"}) ;
							window.close() ;
						},error:function(){
							alert("操作出现异常，请检查输入是否正确或者该类型成本是否已经存在！") ;
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
<input type="hidden" id="ID" value="<?php echo $id;?>"/>
<input type="hidden" id="ASIN" value="<?php echo $asin;?>"/>
<fieldset class="purchasing_officer area">
	<legend>采购专员</legend>
	<table>
		<tr>
			<td>采购费用：</td><td><input class="cost"  type="text" id="PURCHASE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["PURCHASE_COST"];?>"/></td>
		</tr>
	</table>
</fieldset>
<fieldset class="transfer_specialist area">
	<legend>物流专员</legend>
	<table>
		
		<tr>
			<td>入库前物流费用：</td><td><input class="cost"   type="text" id="BEFORE_LOGISTICS_COST" value="<?php echo $productCost[0]["sc_product_cost"]["BEFORE_LOGISTICS_COST"];?>"/></td>
			<td>关税：</td><td><input class="cost"   type="text" id="TARIFF" value="<?php echo $productCost[0]["sc_product_cost"]["TARIFF"];?>"/></td>
		</tr>
		<tr>
			<td>仓储费用 ：</td><td><input class="cost"   type="text" id="WAREHOURSE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["WAREHOURSE_COST"];?>"/></td>
			<td>USPS邮费 ：</td><td><input  class="cost"   type="text" id="USPS_COST" value="<?php echo $productCost[0]["sc_product_cost"]["USPS_COST"];?>"/></td>
		</tr>
		
	</table>
</fieldset>

<fieldset class="product_specialist area">
	<legend>产品专员</legend>
	<table>
		<tr>
			<td>成本类型：</td><td>
				<select id="TYPE">
					<option value=""></option>
					<option value="FBM" <?php if($productCost[0]["sc_product_cost"]["TYPE"] == 'FBM') echo 'selected';?> >FBM</option>
					<option value="FBA" <?php if($productCost[0]["sc_product_cost"]["TYPE"] == 'FBA') echo 'selected';?>>FBA</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>amazon佣金：</td><td><input class="cost"   type="text" id="AMAZON_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["AMAZON_FEE"];?>"/></td>
			<td>可变关闭费用：</td><td><input class="cost"   type="text" id="VARIABLE_CLOSURE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["VARIABLE_CLOSURE_COST"];?>"/></td>
		</tr>
		<tr>
			<td>标签费用 ：</td><td><input class="cost"   type="text" id="TAG_COST" value="<?php echo $productCost[0]["sc_product_cost"]["TAG_COST"];?>"/></td>
			<td>打包费：</td><td><input class="cost"   type="text" id="PACKAGE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["PACKAGE_COST"];?>"/></td>
		</tr>
		<tr>
			<td>订单处理费：</td><td><input class="cost"   type="text" id="OORDER_PROCESSING_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["OORDER_PROCESSING_FEE"];?>"/></td>
			<td>稳重费 ：</td><td><input class="cost"   type="text" id="STABLE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["STABLE_COST"];?>"/></td>
		</tr>
	</table>
</fieldset>

<fieldset class="cashier area">
	<legend>会计</legend>
	<table>
		<tr>
			<td>当地税费  ：</td><td><input class="cost"   type="text" id="LOST_FEE" value="<?php echo $productCost[0]["sc_product_cost"]["LOST_FEE"];?>"/></td>
			<td>人工成本：</td><td><input  class="cost"  type="text" id="LABOR_COST" value="<?php echo $productCost[0]["sc_product_cost"]["LABOR_COST"];?>"/></td>
		</tr>
		<tr>
			<td> 服务成本  ：</td><td><input class="cost"   type="text" id="SERVICE_COST" value="<?php echo $productCost[0]["sc_product_cost"]["SERVICE_COST"];?>"/></td>
		</tr>
	</table>
</fieldset>

<fieldset class="general_manager area">
	<legend>总经理</legend>
	<table>
		<tr>
			<td>其他成本 ：</td><td><input class="cost"   type="text" id="OTHER_COST" value="<?php echo $productCost[0]["sc_product_cost"]["OTHER_COST"];?>"/></td>
		</tr>
	</table>
</fieldset>

<div class="alert alert-info area" style="width:50%;">
总成本:&nbsp;<input type="text" id="TOTAL_COST" readonly="readOnly" value="<?php echo $productCost[0]["sc_product_cost"]["TOTAL_COST"];?>"/>
</div>
	<table>
		<tr>
			<td></td><td><button type="submit" class="btn btn-primary">保存</button></td>
		</tr>
	</table>
</form>
</html>