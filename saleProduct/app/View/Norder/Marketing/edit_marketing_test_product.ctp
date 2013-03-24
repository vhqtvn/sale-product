<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <?php echo $this->Html->charset(); ?>
    <title></title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
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
		
		$id = $product[0]['sc_marketing_test_details']["ID"] ;
		$asin = $product[0]['sc_marketing_test_details']["ASIN"] ;
		$title = $product[0]['sc_product']["TITLE"] ;
		$planId = $product[0]['sc_marketing_test_details']["PLAN_ID"] ;
		
		$guidePrice = $product[0]['sc_marketing_test_details']["GUIDE_PRICE"] ;
		$providor = $product[0]['sc_marketing_test_details']["PROVIDOR"] ;
		
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
					url:contextPath+"/marketing/saveMarketingTestProduct",
					data:{
						id:$("#id").val(),
						providor:$("#providor").val(),
						guide_price:$("#guide_price").val()
					},
					cache:false,
					dataType:"text",
					success:function(result,status,xhr){
						window.opener.$(".grid-content-details").llygrid("reload",{planId:'<?php echo $planId;?>'}) ;
						window.close() ;
					}
				}); 
			})
			
			$(".edit_supplier").click(function(){
				openCenterWindow(contextPath+"/supplier/listsSelect/<?php echo $asin ;?>",800,600) ;
				return false;
			}) ;
		})
   </script>

</head>
<body>
	<input id="id" value='<?php echo $id ;?>' type="hidden"/>
	<table class="table table-bordered">
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
			<td>试销价格：</td>
			<td><input id="guide_price" type="text" value='<?php echo $guidePrice ;?>' /></td>
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
			</select> <a href="<?php echo $contextPath;?>/supplier/listsSelect/B00005NPOB" class="edit_supplier">编辑产品供应商</a>
			</td>
		</tr>
		<tr>
			<td></td><td><button class="btn btn-primary">保存</button></td>
		</tr>
	</table>

</html>