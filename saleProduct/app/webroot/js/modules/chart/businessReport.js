$(function () {
	
	function  loadTypeChart( type ){
		$(".tc").hide() ;
		$(".type"+type+"-container").show() ;

		var asins = {} ;			
		$.dataservice("model:Chart.BusinessReportChart.load",{realId: $realId , type:type },function(result){
			
			//alert( $.json.encode(result) ) ;
			
			asins= {} ;
			result = result.records ;
			
			var categories = [] ;
			var categoriesMap = {} ;
			//init category
			$(result).each(function(){
				if( !categoriesMap[this.START_TIME+">"+this.END_TIME] ){
					categories.push( this.START_TIME+">"+this.END_TIME ) ;
					categoriesMap[ this.START_TIME+">"+this.END_TIME] = true ;
				}
			}) ;

			//init series type
			var seriresName = {} ;
			$(result).each(function(){
				seriresName[this.SERIRES_NAME] = [] ;
			}) ;

			//init series
			var seriesData = {} ;
			for(var ASIN in seriresName){
				var rs =  ASIN  ;
					$(categories).each(function(index , pDate){
							var hasData = false ;
							$(result).each(function(){
									if( this.SERIRES_NAME == rs && (this.START_TIME+">"+this.END_TIME) == pDate ){
										hasData = true ;
										seriresName[rs].push( parseInt(this.DAY_PAGEVIEWS) ) ;
									}
							}) ;
							if(!hasData){
								seriresName[rs].push( 0) ;
						   }
				}) ;
			}

			//format series
			var series = [] ;
			for(var o in seriresName){
				series.push({name:o,data:seriresName[o]}) ;
				}

			$('.container',".type"+type+"-container").highcharts({
	            chart: {
	                type: 'line'
	                	//type: 'column'
	            },
	            title: {
	                text: '销售报告 '
	            },
	            subtitle: {
	                text: ''
	            },
	            credits: {
	           	 	enabled: false
	            },
	            xAxis: {
	                categories: categories
	            },
	            yAxis: {
	                min:0,
	                title: {
	                    text: 'Day PageViews'
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

	        //渲染竞争信息链接
			//[offer-listing]

			$(".offer-container",".type"+type+"-container").empty();
			for(var asin in asins){
				$(".offer-container",".day-container").append("<li style='float:left;margin:2px 5px;' class='alert alert-info'><a offer-listing='"+asin+"'>"+asins[asin]+"("+asin+")"+"</a></li>") ;
			}
		}) ;
	}
	
		function loadChart(){
			var type = $("[name='type']:checked").val() ;
			loadTypeChart(type) ;
		}
		loadChart() ;
		$("[name='type']").click(function(){
			loadChart() ;
		}) ;
		
    });