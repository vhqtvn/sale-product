<!DOCTYPE HTML>
<html>
	<head>
	<?php echo $this->Html->charset(); ?>
    <title>货品统计图</title>
    <meta http-equiv="pragma" content="no-cache"/>
	<meta http-equiv="cache-control" content="no-cache"/>

   <?php
  		 include_once ('config/config.php');
   
		echo $this->Html->meta('icon');
		echo $this->Html->css('../js/validator/jquery.validation');
		echo $this->Html->css('default/style');

		echo $this->Html->script('jquery');
		echo $this->Html->script('common');
		echo $this->Html->script('jquery.json');
		echo $this->Html->script('highcharts/highcharts');
		echo $this->Html->script('calendar/WdatePicker');
		echo $this->Html->script('highcharts/modules/exporting.src');
		echo $this->Html->script('modules/chart/salechart');
		
		$sku = $params['arg1'] ;
		
		
	?>
		<script type="text/javascript">
			var sku = '<?php echo $sku;?>' ;
		</script>
	</head>
	<body>
			<div>
				<input type="radio"  value="1"  name="type" checked/>日销量统计图
				<input type="radio"  value="2"  name="type"  />月销量统计图
			</div>

			<div  class="day-container">
				<div class="toolbar toolbar-auto query-bar">
					<table style="width:100%;" class="query-table">	
						<tr>
							<th>选择月份:</th>
							<td>
								<input  type="text" class="Wdate  daychart-month"  value="<?php echo $showtime=date("Y-m");?>" onclick="WdatePicker({dateFmt:'yyyy-MM',minDate:'2000-1',maxDate:'<?php echo $showtime=date("Y-m");?>'})" readonly="readonly" />
							</td>
							<td>
								<button class="btn  reload-daychart">确定</button>
							</td>
						</tr>						
					</table>
				</div>
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
			
			<div  class="month-container">
				<div class="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
				<ul class="offer-container" style="list-style: none;text-align:right;"></ul>
			</div>
	</body>
</html>
