<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>营销产品列表</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>
    <?php
   		include_once ('config/config.php');
  		include_once ('config/header.php');
		echo $this->Html->script('modules/account/product_list_cost_bad');

		echo $this->Html->css('../js/modules/tag/tagutil');
		echo $this->Html->script('modules/tag/tagutil');
		
		$group=  $user["GROUP_CODE"] ;
	?>
	
    <script type="text/javascript">

   var accountId = '' ;
   </script>
   
   <style style="text/css">
   		*{
   			font:12px "微软雅黑";
   		}
   		
   		.rights-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:red;
   			display:block;
   			float:left;
   		}
   		
   		.ranking-warning-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#800000;
   			display:block;
   			float:left;
   		}
   		
   		.country-area-flag{
   			width:10px;
   			height:10px;
   			margin-top:5px;
   			background:#0000FF;
   			display:block;
   			float:left;
   		}
   		
   		.lly-grid-cell-input{
   		}
   		
   		.query-bar ul{
   			display:block;
   			margin_bottom:5px;
   			height:auto;
   			width:100%;
   		}
   		
   		.query-bar ul li{
   			list-style-type:none;
   			float:left;
   			padding:3px 0px;
   		}
   		
   		.query-bar ul li label{
   			float:left;
   			margin:0px 0px;
   			margin-left:15px;
   		}
   		
   		.query-bar{
   			clear:both;
   		}
   		
   		li select,li input{
   			width:auto;
   			padding:0px;
   		}
   		
   		.popover-inner  .popover-title{
			font-size:12px;
   		}
   </style>
   
   <style type="text/css">
		.flow-node{
			min-width:50px; 
			height:20px; 
			border:5px solid #0FF; 
			border-radius:5px;
			font-weight:bold;
			cursor:pointer;
		}
		
		.flow-node.active{
			border-color:#3809F7 ;
			background-color:#3809F7 ;
			color:#EEE;
		}
		
		.flow-node.passed{
			border-color:#92E492 ;
			background-color:#92E492 ;
			
		}
		
		.flow-node.termination{
			color:red;
	        background-color:pink ;
			border-color:pink;
		    white-space: nowrap;
		}
		
		.flow-node.disabled{
			border-color:#CCC ;
			background-color:#CCC ;
			color:#EEE;
		}
		
		.flow-table{
			text-align:center;
			margin:5px 0px;
		}
		

		.flow-action{
			position:absolute;;
			right:10px;
			top:48px;
			z-index:100;
		}
		
		.flow-split{
			font-size:30px;
		}
		
		.memo{
			position:absolute;
			top:85px;
			z-index:1;
			right:10px;
			width:300px;
			height:50px;
			background:#ffd700;
			display:none;
		}
		
		.memo-control{
			display:none;
		}
		
		.tag-container li{
			float:left;
			list-style: none;
			margin:2px 5px;
		 	padding:2px;
		}
		
		.count{
			font-weigbt:bold;
			color:red;
		}
	</style>

</head>
<body style="magin:0px;padding:0px;">

				<div class="flow-bar1">
		<center><table class="flow-table">						
		<tbody>
		<tr>	
		<td>	&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><div class="flow-node active " status="1">采购成本不完整<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="2">售价不完整<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="3">Amazon费用缺失<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="4">重量缺失<span class="count"></span></div></td>
		<td class="flow-split">-</td><td><div class="flow-node disabled" status="5">利润不准确<span class="count"></span></div></td>
		</tr>					
		</tbody>
		</table>										
		</center>
	</div>
			<div style="clear:both;height:5px;"></div>
			<div class="grid-content" style="width:99%;">
			</div>

</body>
</html>
