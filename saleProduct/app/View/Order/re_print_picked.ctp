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
		echo $this->Html->script('jquery.hotkeys');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/order/re_print_picked');
		echo $this->Html->script('grid/query');
	
	?>
	
	<style type="text/css">
		body{
			padding:3px;
		}
		
		.print-title{
			margin:3px 10px 10px 10px;
			font-weight:bold;
			font-size:12px;
			position:relative;
		}
		
		#orderId{
			font-weight:bold;
			color:#FFF;
			font-size:15px;
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
		
		.search{
			margin:10px auto;
			text-align:center;
			position:relative;
		}
		
		.exception{
			position:absolute;
			top:0px;
			right:0px;
			z-index:1;
			text-align:right;
		}
		
		.exception-form{
			background:#ffc0cb ;
			border:1px solid #CCC;
			padding:10px;
			text-align:left;
			display:none;
		}
		
		.exception-form div{
			margin:3px;
		}
		
		.cell-div{
		    overflow:normal;
		    white-space:normal;
			text-overflow:normal;
			word-break : normal; 
		    -o-text-overflow: none;
		}
		
		.message-alert{
			display:none;
			width:100%;
			height:30px;
			font-size:25px;
			color:#000;
			font-weight:bold;
			border:1px solid #CCC;
			margin:0px auto;
			padding-top:20px;
			background:green;
			text-align:center;
			position:absolute;
			bottom:0px;right:0px;left:0px;
		}
		
		div.lly-grid .cell-div span{
			font-size:16px;
			font-weight:bold;
			color:#000;
		}
		
		.order-id {
			width:200px;
			position:absolute;
			left:50px;
			top:0px;
			height:18px;
			padding:3px;
			font-weight:bold;
			color:#000;
		}
	</style>
	
	<script>
		var type = '<?php echo $type;?>'
		$(function(){
			$(".btn-danger").toggle(function(){
				$(".exception-form").show() ;
			},function(){
				$(".exception-form").hide() ;
			}) ;
		}) ;
	</script>
	
	<style media=print type="text/css">  
	    #noprint{visibility:hidden}  
	</style>
   
</head>

<body>
	<?php
		$user = $this->Session->read("product.sale.user") ;
	?>
	<div class="print-title">
		<div class="header" >
			订单二次分拣
		</div>
		
		<table>
			<tr>
				<td style="text-align:right;">
					<nobr>
					<?php echo date('Y-m-d H:i:s')?>(<?php echo $user['NAME']?>)
					</nobr>
				</td>
			</tr>
		</table>
	</div>
	
	<div class="search">
		<input type="input" id="orderId" name="orderId" class="span4" placeHolder="内部订单号 或 产品SKU"/>
		<button class="btn btn-search btn-primary" style="display:none;">search</button>
		<div class="order-id alert alert-info"></div>
		<div class="exception">
			<button class="btn btn-danger">异常处理</button>
			<div class="exception-form">
				<div>
					<label>分类：</label>
					<select id="type">
						<option value="">-</option>
						<option value="exception_value">质量异常</option>
						<option value="exception_package">包装异常</option>
						<option value="exception_ship">物流错误</option>
						<option value="exception_weight">重量超标</option>
					</select>
				</div>
				<div>
					<label>备注:</label>
					<textarea id="memo"></textarea>
				</div>
				<button class="btn btn-primary exception-btn">确认</button>
			</div>
		</div>
	</div>
	
	<div class="grid-content" style="">
	</div>
	
	<div class="message-alert">PASS</div>
</body>
</html>
