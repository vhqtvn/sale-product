<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>货品上架</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
   include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/grid/jquery.llygrid');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/warehouse/in/design');
		
		$warehouse = $result['warehouse'] ;
		$units     = $result['units'] ;
		
		$units = json_encode($units) ;
		
		$blockIndex = date('YmdHis') ;
	?>
  
   <script type="text/javascript">
   	var warehouseId = '<?php echo $warehouse['ID'] ;?>' ;	
   </script>
   
   <script type="text/javascript">
   	var designText = <?php echo $warehouse['DESIGN_TEXT'] ;?> ;
   	var $blockIndex = <?php echo $blockIndex ;?> ;
   </script>
   
   <script type="text/javascript">
   	var units = <?php echo $units;?> ;
   	var array = [] ;
   	var unitMap = {} ;
	$(units).each(function(){
		var row = {} ;
		for(var o in this){
			var _ = this[o] ;
			for(var o1 in _){
				_[o1] && ( row[o1] = _[o1] ) ;
			}
		}
		array.push(row) ;
		unitMap[row['ID']] = row ;
	}) ;
	
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
    		padding:2px 2px;
    		font-weight:bold;
    		width:100px;
    		text-align:center;
    	}
    	
    	.tool-area{
    		width:98%;
    		margin:0px auto;
    		border:1px solid #EEE;
    		padding:10px;
    		min-height:380px;
    		margin-right:5px;
    		background:#EEE;
    	}
    	
		.w-rkm{background:yellow ;}
		.w-ckm{background:yellow ;}
		.w-td{background:#EEE ;}
		.w-kw{background:#eceeed ;z-index:1;}
		.w-hj{background:#c0c0c0 ;z-index:2}
		.w-hw{background:blue ;z-index:3;color:#FFF;font-size:12px;}
		
		.design-area{
			border:1px solid blue;
			min-height:400px;
		}
		
		.active{
			background:#ff00ff
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
			<hr/>
			<button class="btn  btn-danger btn-larger btn-delete disabled" disabled="disabled">&nbsp;&nbsp;删&nbsp;&nbsp;除&nbsp;&nbsp;</button>
		  </div>
		</div>
		<div class="span10">
			<div class="design-area">
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span2">&nbsp;</div>
		<div class="span10">
			<input type="hidden" id="blockId"  value=""/>
			<table class="form-table col3" >
				<caption>货位信息</caption>
				<tbody>										   
					<tr>
						<th>位置编码：</th>
						<td><input type="text" id="code" style="width:400px;" value=""/></td>
						<td rowspan=2>
						<button class="btn btn-primary save-config">保存</button>
						</td>
					</tr>
					<tr>
						<th>备注：</th>
						<td>
							<textarea id="memo" style="width:400px;height:50px;"></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>
