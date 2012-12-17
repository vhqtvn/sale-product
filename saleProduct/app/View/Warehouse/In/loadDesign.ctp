<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>货品上架</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/warehouse/in/design');
	?>
  
   <script type="text/javascript">
   	var warehouseId = '<?php echo $result['ID'] ;?>' ;	
   </script>
   
   <script type="text/javascript">
   	var designText = <?php echo $result['DESIGN_TEXT'] ;?> ;
   </script>
   
    <style type="text/css">
    	.span2 .block{
    		margin-top:5px;
    		width:auto;
    		padding:10px;
    		font-weight:bold;
    		font-size:15px;
    		width:100px;
    		text-align:center;
    	}
    	
    	.design-area .block{
    		margin-top:5px;
    		width:auto;
    		padding:10px;
    		font-weight:bold;
    		font-size:15px;
    		width:100px;
    		text-align:center;
    	}
    	
    	.tool-area{
    		width:98%;
    		margin:0px auto;
    		border:1px solid #EEE;
    		padding:10px;
    		min-height:480px;
    		margin-right:5px;
    		background:#EEE;
    	}
    	
		.w-rkm{background:yellow ;}
		.w-ckm{background:yellow ;}
		.w-td{background:#EEE ;}
		.w-kw{background:#ffd700 ;z-index:1;}
		.w-hj{background:#c0c0c0 ;z-index:2}
		.w-hw{background:blue ;z-index:3;color:#FFF;}
		
		.design-area{
			border:1px solid blue;
			min-height:500px;
		}
		
		button.save{
			font-size:15px;
			padding:10px;
			font-weight:bold;
		}
	</style>

</head>
<body>
	<div class="row-fluid">
		<div class="span2">
		  
		  <div class="tool-area">
		  	<button class="btn btn-primary btn-larger save">保存设计</button>
		  	<br/><br/>
		  
			<div class="block w-rkm" key="rkm">入库门</div>
			<div class="block w-ckm" key="ckm">出库门</div>
			<div class="block w-td"  key="td">通道</div>
			<div class="block w-kw"  key="kw">库位</div>
			<div class="block w-hj"  key="hj">货架</div>
			<div class="block w-hw"  key="hw">货位</div>
		  </div>
		</div>
		<div class="span10">
			<div class="design-area">
			
			</div>
		</div>
	</div>
</body>
</html>
