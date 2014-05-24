<!DOCTYPE HTML>
<html>
	<head>
	<?php echo $this->Html->charset(); ?>
    <title>采购用户统计图</title>
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
		
		//echo  gmdate("Y-m-d%20H:00:00"); 
		ini_set('date.timezone','Asia/Shanghai');
		$sku = $params['arg1'] ;
	?>
		<script type="text/javascript">
		var formatJson = {45:'新增采购单',51:'下单完成',49:'交易完成',50:'完成收货',
				60:'完成验货',80:'完成入库'} ;

		   function getParams(){
                return { startTime:$(".start-time").val() , endTime: $(".end-time").val() } ;
		  }
		
			function  loadOrderCountChart(){
				var accounts = {} ;

				$(".container").height( $(window).height()-80 ) ;

				var params = getParams() ;
				if( !params.startTime ){
					alert("开始时间必须！") ;
					return ;
				}

				$.dataservice("sqlId:sql_report_productPurchase_user_static",getParams(),function(result){

					var categories = [] ;
					var categoryMap = {} ;
					categories.push('新增采购单') ;
					categories.push('下单完成') ;
					categories.push('交易完成') ;
					categories.push('完成收货') ;
					categories.push('完成验货') ;
					categories.push('完成入库') ;
					//init category
					$(result).each(function(index,item){
						item = item.t ;
						item.PD = formatJson[item.PD] ;
						if( !categoryMap[item.PD] ){
							//categories.push(item.PD) ;
						}
						categoryMap[item.PD] = true ;
						item.NAME = item.NAME;//formatJson[item.NAME] ;
					}) ;

					//init series type
					var seriresName = {} ;
					$(result).each(function(index,item){
						item = item.t ;
						accounts[item.NAME] = item.NAME ;
						seriresName[item.NAME] = [] ;
					}) ;

					//init series
					var seriesData = {} ;
					for(var accountName in seriresName){
							$(categories).each(function(index , pDate){
									var hasData = false ;
									$(result).each(function(index,item){
										item = item.t ;
										if( item.NAME == accountName && item.PD == pDate ){
											hasData = true ;
											seriresName[accountName].push( parseInt(item.C) ) ;
										}
									}) ;
									if(!hasData){
										seriresName[accountName].push( 0) ;
								   }
								}) ;
					}

					//format series
					var series = [] ;
					for(var o in seriresName){
						series.push({name:o,data:seriresName[o]}) ;
						}

					$('.container',".day-container").highcharts({
			            chart: {
			                type: 'line'
			                	//type: 'column'
			            },
			            title: {
			                text: '产品采购统计'
			            },
			            subtitle: {
			                text: ''
			            },
			            credits: {
			           	 	enabled: false
			            },
			            xAxis: {
			                categories: categories,
			                labels: {
			                	rotation: -45 ,  //竖直放
			                	style: {  
		                     		 font: 'normal 11px Verdana, sans-serif'  
		                	 	}
			                 }			                
			            },
			            yAxis: {
			                min:0,
			                title: {
			                    text: '流程统计'
			                }
			            },
			            tooltip: {
			                enabled: false,
			                formatter: function() {
			                    return '<b>'+ this.series.name +'</b><br/>'+
			                        this.x +': '+ this.y +'°C';
			                }
			            },
			            plotOptions: {
			                line: {
			                    dataLabels: {   enabled: true  },
			                    enableMouseTracking: false
			                }
			            },
			            series:series
			        });


					//////////////////////////////
					setTimeout(function(){
						loadOrderCountChart() ;
					},120000) ;
			        
				},{noblock:true}) ;
			}

			$(function(){
				loadOrderCountChart() ;
			}) ;

			
		</script>
	</head>
	<body>
			<div class="toolbar toolbar-auto query-bar">
					<table  class="query-table">	
						<tr>
							<th>开始时间:</th>
							<td>
								<input  type="text" class="Wdate  start-time"  value="<?php echo $showtime=date("Y-m-d");?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" readonly="readonly" />
							</td>
							<th>结束时间:</th>
							<td>
								<input  type="text" class="Wdate  end-time"  value="<?php echo $showtime=date("Y-m-d");?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})" readonly="readonly" />
							</td>
							<td>
								<button class="btn  reload-daychart no-disabled"  onclick="loadOrderCountChart()">确定</button>
							</td>
						</tr>						
					</table>
				</div>
			<div  class="day-container">
				<div class="container" style="min-width: 400px; margin: 0 auto"></div>
			</div>
			<iframe src=""  id="asyncOrder" style="display:none;"></iframe>
	</body>
</html>
