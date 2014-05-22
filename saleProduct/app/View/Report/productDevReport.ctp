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
			var formatJson = {10:'新增开发产品',20:'产品分析完成',30:'询价完成',50:'审批完成',
					60:'货品录入完成',70:'制作Listing完成',72:'Listing审批完成',80:'已采购'} ;
			/*var formatJson = {10:'新增开发产品',15:'废弃',20:'产品分析完成',25:'成本利润分析',30:'产品经理审批',40:'总监审批',42:"样品检测",44:"检测审批",50:'录入货品',
			60:'制作Listing',70:'Listing审批',72:'采购试销',74:'库存到达',76:'营销展开',78:'开发总结',80:'处理完成'} ;*/
		
			function  loadOrderCountChart(){
				var accounts = {} ;

				$(".container").height( $(window).height()-80 ) ;

				$.dataservice("sqlId:sql_report_productDev_static",{},function(result){

					var categories = [] ;
					var categoryMap = {} ;
					//init category
					$(result).each(function(index,item){
						item = item.t ;
						if( !categoryMap[item.PD] ){
							categories.push(item.PD) ;
						}
						categoryMap[item.PD] = true ;
						item.NAME = formatJson[item.NAME] ;
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
			                text: '产品开发统计'
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
