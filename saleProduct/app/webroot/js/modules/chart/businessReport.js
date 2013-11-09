$(function () {
	$("[name='type']").click(function(){
		//loadChart() ;
		var val = $(this).val() ;
		var params = {} ;
		if(val == 1){
			params = {realId: $realId, pkey:"PARENT_ASIN" , key:"ASIN",parentAsin:1,parentChildAsin:''} ;
		}else if(val == 2){
			params = {realId: $realId, pkey:"ASIN" , key:"ASIN",parentAsin:'',parentChildAsin:'1'} ;
		}else if(val == 3){
			params = {realId: $realId, pkey:"SKU" , key:"SKU",parentAsin:'',parentChildAsin:''} ;
		}
		
		$(".grid-content").llygrid("reload",params) ;
	}) ;
	
	$(".grid-content").llygrid({
		columns:[
           	{align:"center",key:"PARENT_ASIN",label:"父ASIN", width:"90",format:function(val,record){
           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
           	}},
           	{align:"center",key:"ASIN",label:"子ASIN", width:"90",format:function(val,record){
           		val = val||"" ;
           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
           	}},
           	{align:"center",key:"SKU",label:"SKU", width:"90",format:function(val,record){
	           	val = val||"" ;
           		return "<a href='#' class='product-detail' asin='"+val+"'>"+val+"</a>" ;
           	}},
           	{align:"center",key:"TITLE",label:"TITLE",width:"20%",forzen:false,align:"left",format:function(val,record){
           		return "<a href='"+contextPath+"/page/forward/Platform.asin/"+record.ASIN+"' target='_blank'>"+val+"</a>" ;
           	}},
           	
           	{align:"right",key:"PAGEVIEWS",label:"总流量", width:"8%"},
        	{align:"right",key:"DAY_PAGEVIEWS",label:"每日流量", width:"8%"},
           	{align:"center",key:"PAGEVIEWS_PERCENT",label:"PageviewsPercent", width:"12%"},
           	{align:"center",key:"BUY_BOX_PERCENT",label:"BuyBoxPercent", width:"10%"},
           	{align:"center",key:"UNITS_ORDERED",label:"UnitsOrdered", width:"10%"},
           	{align:"center",key:"ORDERED_PRODUCT_SALES",label:"OrderedProductSales", width:"15%"},
           	{align:"center",key:"ORDERS_PLACED",label:"OrderPlaced", width:"10%"},
        	{align:"center",key:"START_TIME",label:"开始时间", width:"10%"},
        	{align:"center",key:"END_TIME",label:"结束时间", width:"10%"},
        	{align:"center",key:"CREATTIME",label:"上传时间", width:"10%"},
           	{align:"center",key:"CREATE_TIME",label:"CREATE_TIME", width:"10%"},
           	{align:"center",key:"CREATOR",label:"CREATOR", width:"10%"}
         ],
         ds:{type:"url",content:contextPath+"/grid/query/"},
		 limit:20,
		 pageSizes:[10,20,30,40],
		 height:function(){
			return  $(window).height()-120 ;
		 },
		 title:"最新流量",
		 indexColumn:true,
		 querys:{sqlId:"sql_flow_listAllByRealId",realId: $realId, pkey:"PARENT_ASIN" , key:"ASIN",parentAsin:1 },
		 loadMsg:"数据加载中，请稍候......"
	}) ;
	
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
		//loadChart() ;
		$("[name='type']").click(function(){
			//loadChart() ;
		}) ;
		
    });