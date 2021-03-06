$(function(){
	//初始化流程数据
	var flow = new Flow() ;
	flow.init(".flow-bar center",flowData) ;
	flow.draw(currentStatus) ;
	
	var tabs = [
				{label:'基本信息',iframe:true,url:contextPath+"/page/model/Warehouse.In.edit/"+inId,id:'t1'},//9
				{label:'入库货品(Listing)',iframe:true,url:contextPath+"/page/forward/Warehouse.In.inProductList/"+inId,id:'t2'},
				{label:'包装箱',iframe:true,url:contextPath+"/page/model/Warehouse.In.editBox/"+inId,id:'t3'},
				{label:'跟踪状态',iframe:true,url:contextPath+"/page/model/Warehouse.In.editTrack/"+inId,id:'t4'}
			] ;
	if( inSourceType == 'fba' ){
		//tabs.push( {label:'FBA入库计划(本地)',iframe:true,url:contextPath+"/page/forward/SupplyChain.edit_inbound/"+fbaLocalId,id:'t4'} );
		//tabs.push( {label:'FBA入库计划(Amazon)',iframe:true,url:contextPath+"/page/forward/SupplyChain.list_inbound/"+fbaLocalId,id:'t4'} );
	}
	
	var tab = $('#details_tab').tabs( {
		tabs:tabs ,
		height:'520px',
		select:function(event,ui){
			var index = ui.index ;
			//renderAction(index);
		}
	}) ;
}) ;

var Flow = function(){
	var _data = null ;
	var _selector = null ;
	var itemTemplate = '<td><div class="flow-node {statusClass}" status="{status}">{label}</div></td>' ;
	
	this.init = function(selector , d){
		_data = d ;
		_selector = selector ;
		return this ;
	}

	this.draw = function(current){
		//create container
		var html = '<table class="flow-table">\
						<tr>\
						</tr>\
					</table>\
					<div class="flow-action">\
						<div class="btn-container"></div>\
						<a href="#" class="memo-control">附加备注</a>\
					</div>\
					<textarea class="memo" placeHolder="输入附加备注信息"></textarea>' ;
		
		$(_selector).empty().html(html) ;
		
		$(".memo-control").toggle(function(){
			$(".memo").show() ;
		},function(){
			$(".memo").hide() ;
		}) ;
		
		var flowContainer = $(_selector).find(".flow-table tr")
		
		var length = _data.length ;
		$(_data).each(function(index){
			var statusClass = current == this.status ?"active":(this.status < current?"passed":"disabled") ;
			var status = this.status ;
			var isMemo = this.memo ;
			var label = this.label ;
			html =  itemTemplate.replace(/{statusClass}/g,statusClass)
								.replace(/{status}/g,status)
								.replace(/{label}/g,label) ;
			flowContainer.append(html) ;
			
			if(length != index+1){
				flowContainer.append("<td class='flow-split'>-</td>") ;
			}
			
			
			if( current == this.status ){
				var actions = this.actions ;
				
				if(this.memo && actions && actions.length >=1 ){
					$(".memo-control").show();
				}
				
				$(actions||[]).each(function(){
					var me = this ;
					$("<button class='btn btn-primary' style='margin-right:3px;'>"+this.label+"</button>&nbsp;&nbsp;")
						.appendTo(".btn-container").click(function(){
							me.action() ;
						}) ;  ;
				}) ;
			}
			
		}) ;
	}
} ;

