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
						cost:$("#cost").val(),
						providor:$("#providor").val(),
						sample:$("#sample").val(),
						sample_code:$("#sample_code").val(),
						area:$("#area").val()
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
	<label>备注：</label>
	<textarea name="name" style="width:98%;height:150px;">text</textarea>
	<br/><br/>
	<button class="btn btn-primary"><?php
		if( $status == 2 ){
			echo "申请采购" ;
		}else if( $status == 3 ){
			echo "审批通过" ;
		}else if( $status == 4 ){
			echo "审批不通过" ;
		}else if( $status == 4 ){
			echo "确认采购" ;
		}
	?></button>
</body>
</html>