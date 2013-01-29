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
		echo $this->Html->css('../js/tab/jquery.ui.tabs');
		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.ui');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('grid/jquery.llygrid');
		echo $this->Html->script('modules/warehouse/in/designView');
		echo $this->Html->script('tab/jquery.ui.tabs');
		
		$warehouse = $result['warehouse'] ;
		$units     = $result['units'] ;
		
		$units = json_encode($units) ;
		
	?>
  
   <script type="text/javascript">
    var actionType = 'view' ;
   	var warehouseId = '<?php echo $warehouse['ID'] ;?>' ;	
   </script>
   
   <script type="text/javascript">
   	var designText = <?php echo $warehouse['DESIGN_TEXT'] ;?> ;
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

    	.design-area .block{
    		margin-top:5px;
    		width:auto;
    		padding:2px 2px;
    		font-weight:bold;
    		width:100px;
    		text-align:center;
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
		
		.design-view{
			position:absolute;
			top:2px;
			right:2px;
		}
		
		.add-unitProduct{
			position:absolute;
			right:50px;
			top:5px;
			z-index:100;
		}
	</style>

</head>
<body>
	<div class="row-fluid">
		<div class="span12">
			<button class="btn btn-primary design-view">设计视图</button>
			<div class="design-area">
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12" style="position:relative;">
			<button class="btn  btn-primary add-unitProduct" style="display:none;" onclick="addUnitProduct();return false;">添加库位货品</button>
			<div id="tabs-default"></div>
			<div id="base-tab">
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
			
			<div id="product-tab">
				<div class="product-grid" style="width:800px"></div>
			</div>
				
		</div>
	</div>
</body>
</html>
