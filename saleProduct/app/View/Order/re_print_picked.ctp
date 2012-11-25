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
		echo $this->Html->script('jquery.hotkeys');
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
	</style>
	
	<script>
		var type = '<?php echo $type;?>'
		$(function(){
			var text = "" ;
			if(type == 1){
				text = "订单二次分拣" ;
			}else if(type == 2){
				text = "订单二次分拣" ;
			}
			$(".header").html( text+"("+ window.opener.currentPickName+")");
			
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
			拣货单（XXXXXXXX）
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
		<input type="input" id="orderId" name="orderId" class="span4" placeHolder="ORDER ID"/>
		<button class="btn btn-search btn-primary">search</button>
		
		<div class="exception">
			<button class="btn btn-danger">异常处理</button>
			<div class="exception-form">
				<div>
					<label>分类：</label>
					<select id="type">
						<option value="value1">option1</option>
						<option value="value2">option2</option>
						<option value="value3">option3</option>
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
</body>
</html>
