<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>拣货单</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

  
    <script type="text/javascript">
    	var pickedId = "<?php echo $pickId;?>"
     	var accountId = "" ;
     	var status = "5" ;
	</script>
   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('default/style');
		echo $this->Html->css('../js/grid/jquery.llygrid');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/norder/export_picked');
		echo $this->Html->script('grid/query');
		
	?>
	
	<style type="text/css">
		body{
		}
		
		.print-title{
			margin:3px 10px 10px 10px;
			font-weight:bold;
			font-size:12px;
			position:relative;
		}
		
		.print-title table{
			position:absolute;
			width:250px;
			right:0px;
			top:10px;
		}
		
		.lly-grid-pager{
			display:none;
		}
		
		.header{
			text-align:center;
			/*border-bottom:1px solid #CCC;*/
			padding-bottom:10px;
			margin-bottom:3px;
			font-size:20px;
		}
		
		.table tr th, .table tr td {
			word-break:none;
		}
	</style>
	
	<script type="text/javascript">
		$(function(){
			$(".header").html( "导出拣货单("+ window.opener.currentPickName+")");
	
			$(".print-btn").click(function(){
				printPicked();
			}) ;
		}) ;
		
		function printPicked(){
			
			window.location.href = "/saleProduct/index.php/order/doExportPicked/<?php echo $pickId;?>"
					/*$.ajax({
						type:"post",
						url:"/saleProduct/index.php/order/exportPicked/<?php echo $pickId;?>" ,
						data:{},
						cache:false,
						dataType:"text",
						success:function(result,status,xhr){
							$('.noprint').remove();
							window.print();
						}
					});*/
			
			
		}
	</script>
   
</head>

<body>
	<?php
		
		$user = $this->Session->read("product.sale.user") ;
		
	?>
	<div class="print-title">
		<div class="header" >
			拣货单（XXXXXXXX）
		</div>
		
		<table>
			<tr>
				<td style="text-align:right;">
					<nobr>
					<?php echo date('Y-m-d H:i:s')?>(<?php echo $user['NAME']?>)
					<button class="noprint print-btn btn">导出</button>
					</nobr>
				</td>
			</tr>
		</table>
		
	</div>
	<div class="grid-content" >
	
	</div>
</body>
</html>
