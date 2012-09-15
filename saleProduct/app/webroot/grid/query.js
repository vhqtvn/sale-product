function Query(scripts,container){
	 this.scripts = scripts ;
	 this.container = container ;
}
/*
var ruleScripts =[
			{label:"Ranking",key:"sc_sale_potential_ranking.ranking",type:'number',min:1,max:10},
			{label:"Ranking",key:"#target_profit",type:'number',min:1,max:10},
			{label:"��������",key:"sc_sale_competition.total_num",type:'number',min:1,max:10},
	];
*/
Query.prototype = {
	render:function(){
			var html = [] ;
			var me = this ;
			$(this.scripts).each(function(){
					html.push( me.input(this) ) ;
			}) ;
			 this.container.html(html.join("")) ;
			 return this ;
	},
	input:function(options){
		if(!options.key) return "" ;
		var html = [] ;
		html.push('<div class="query-item" render-type="number" key="'+options.key+'" relation="'+options.relation+'">') ;
		html.push('	<div class="query-label">') ;
		html.push('		<label>'+options.label+':</label>') ;
		html.push('	</div>') ;
		html.push('	<div class="relation-label">') ;
		html.push('		<label>'+options.relationLabel+'</label>') ;
		html.push('	</div>') ;
		html.push('	<div class="query-content">') ;
		html.push( getRenderInput(options.relation,options.val) ) ;

		html.push('	</div>') ;
		html.push('</div>') ;
		return html.join("") ;
	},
	number:function(options){
		var html = [] ;
		html.push('<div class="query-item" render-type="number" key="'+options.key+'">') ;
		html.push('	<div class="query-label">') ;
		html.push('		<label>'+options.label+'</label>') ;
		html.push('	</div>') ;
		html.push('	<div class="query-content">') ;
		html.push('	<input type="input" name="min" value="'+(options.min||'')+'"/> to ') ;
		html.push('	<input type="input" name="max" value="'+(options.max||'')+'"/>') ;

		html.push('	</div>') ;
		html.push('</div>') ;
		return html.join("") ;
	},
	fetch: function(){
		var queryString = [] ;
		this.container.find("[render-type]") .each(function(){
			var type = $(this).attr("render-type") ;
			var key   = $(this).attr("key") ;
			var relation = $(this).attr("relation") ;
			var item = {} ;
			item.key = key ;
			item.type = type ;
			item.relation = relation ;
			item.value = getRenderInputValue( $(this).find(".query-content") , relation )

			queryString.push(item) ;
		}) ;
		return queryString ;
	}
} ;

function getRenderInput(relation,val){
		if( relation == '>' || relation== '<' || relation =='=' || relation == '<=' || relation == '>=' || relation == "like"){
				return "<input type='text' class='value' value='"+val+"'>"
		}

		return "" ;
}

function getRenderInputValue(el ,relation){
		if( relation == '>' || relation== '<' || relation =='=' || relation == '<=' || relation == '>='||relation == "like"){
				return  $(el).find("input").val() ;
		}

		return "" ;
}