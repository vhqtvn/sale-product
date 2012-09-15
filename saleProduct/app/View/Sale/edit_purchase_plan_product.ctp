<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title></title>
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
		
		$id = $product[0]['sc_purchase_plan_details']["ID"] ;
		$asin = $product[0]['sc_purchase_plan_details']["ASIN"] ;
		$title = $product[0]['sc_product']["TITLE"] ;
		$planId = $product[0]['sc_purchase_plan_details']["PLAN_ID"] ;
		
		$cost = $product[0]['sc_purchase_plan_details']["COST"] ;
		$plan_num = $product[0]['sc_purchase_plan_details']["PLAN_NUM"] ;
		$quote_price = $product[0]['sc_purchase_plan_details']["QUOTE_PRICE"] ;
		$providor = $product[0]['sc_purchase_plan_details']["PROVIDOR"] ;
		$sample_code = $product[0]['sc_purchase_plan_details']["SAMPLE_CODE"] ;
		$sample  = $product[0]['sc_purchase_plan_details']["SAMPLE"] ;
		$area = $product[0]['sc_purchase_plan_details']["AREA"] ;
		
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
		$(function(){
			
			$("button").click(function(){
				
				$.ajax({
					type:"post",
					url:"/saleProduct/index.php/sale/savePurchasePlanProduct",
					data:{
						id:$("#id").val(),
						plan_num:$("#plan_num").val(),
						quote_price:$("#quote_price").val(),
						cost:'',
						providor:$("#providor").val(),
						sample:$("#sample").val(),
						sample_code:$("#sample_code").val(),
						area:$("#area").val(),
						memo:$("#memo").val()
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.$(".grid-content-details").llygrid("reload",{planId:'<?php echo $planId;?>'}) ;
						window.close() ;
					}
				}); 
			});
			
			$(".edit_supplier").click(function(){
				openCenterWindow("/saleProduct/index.php/supplier/listsSelect/<?php echo $asin ;?>",800,600) ;
				return false;
			}) ;
		})
   </script>

</head>
<body>
	<input id="id" value='<?php echo $id ;?>' type="hidden"/>
	<table class="table">
		<tr>
			<td>编号：</td><td><?php echo $id ;?></td>
		</tr>
		<tr>
			<td>ASIN：</td><td><?php echo $asin ;?></td>
		</tr>
		<tr>
			<td>标题：</td><td><?php echo $title ;?></td>
		</tr>
		<tr>
			<td>采购数量：</td>
			<td><input id="plan_num" type="text" value='<?php echo $plan_num ;?>' /></td>
		</tr>
		<tr>
			<td>采购价：</td>
			<td><input id="quote_price" type="text" value='<?php echo $quote_price ;?>' /></td>
		</tr>
		<tr>
			<td>供应商：</td><td>
			<select id="providor">
				<option value="">--</option>
			<?php
				foreach($supplier as $suppli){
					$temp = "" ;
					if( $suppli['sc_supplier']['ID'] == $providor ){
						$temp = "selected" ;
					}
					echo "<option $temp value='".$suppli['sc_supplier']['ID']."'>".$suppli['sc_supplier']['NAME']."</option>" ;
				}
			?>
			</select>  <a href="/saleProduct/index.php/supplier/listsSelect/B00005NPOB" class="edit_supplier">编辑产品供应商</a>
			</td>
		</tr>
		<tr>
			<td>样品：</td><td>
			<select id="sample">
				<option value="0" <?php if($sample == 0 ) echo 'selected' ;?>>无</option>
				<option value="1" <?php if($sample == 1 ) echo 'selected' ;?> >准备中</option>
				<option value="2" <?php if($sample == 2 ) echo 'selected' ;?>>有</option>
			</select>
			</td>
		</tr>
		<tr>
			<td>样品编码：</td><td>
			<input type="text" id="sample_code" value='<?php echo $sample_code ;?>' />(位置码+产品码组成，中间以下划线连接)
			</td>
		</tr>
		<tr>
			<td>采购地区：</td><td>
			<select id="area">
				<option value="china" <?php if($area == 'china' ) echo 'selected' ;?>>大陆</option>
				<option value="taiwan" <?php if($area == 'taiwan' ) echo 'selected' ;?> >台湾</option>
				<option value="american" <?php if($area == 'american' ) echo 'selected' ;?>>美国</option>
			</select>
			</td>
		</tr>
		<tr>
			<td>采购原因：</td><td>
			<textarea style="width:500px;height:80px;" id="memo"><?php echo $product[0]['sc_purchase_plan_details']['MEMO'] ;?></textarea>
			</td>
		</tr>
		<tr>
			<td></td><td><button class="btn btn-primary">保存</button></td>
		</tr>
	</table>

</html>