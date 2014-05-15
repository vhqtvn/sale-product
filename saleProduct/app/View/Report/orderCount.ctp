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
		
		//echo  gmdate("Y-m-d%20H:00:00"); 
		
		$sku = $params['arg1'] ;
	?>
		<script type="text/javascript">
			function  loadOrderCountChart(){
				var accounts = {} ;

				$(".container").height( $(window).height()-80 ) ;

				$.dataservice("sqlId:sql_report_chart_forOrderAccount",{},function(result){

					var categories = [] ;
					var categoryMap = {} ;
					//init category
					$(result).each(function(index,item){
						item = item.t ;
						if( !categoryMap[item.PD] ){
							categories.push(item.PD) ;
						}
						categoryMap[item.PD] = true ;
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
			                text: '账户下单总销量'
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
			                	rotation: -90   //竖直放
			                 }			                
			            },
			            yAxis: {
			                min:0,
			                title: {
			                    text: '日销量（Sales）'
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


				$(".query-btn").click(function(){
					var now = new Date() ;
					var date = now.getUTCDate() ;
					var year = now.getUTCFullYear() ;
					var month = now.getUTCMonth() +1;
					var hour = now.getUTCHours() ;
					if(month <10){
							month = "0"+month ;
						}
					if(hour <10){
						hour = "0"+hour ;
					}
					if(date <10){
						date = "0"+date ;
					}
					var _d = year+"-"+month+"-"+date+"%20"+hour+":00:00" ;
					$("#asyncOrder").attr("src","http://cyberkin.org/saleProductService/index.php/taskAsynAmazon/listOrder/?LastUpdatedAfter="+_d) ;
				}) ;
			}) ;

			
		</script>
	</head>
	<body>
			<div  class="day-container">
				<div class="container" style="min-width: 400px; margin: 0 auto"></div>
			</div>
			<iframe src=""  id="asyncOrder" style="display:none;"></iframe>
	</body>
</html>
