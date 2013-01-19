function AuditAction(status , statusLabel){
	if(window.confirm("确认【"+statusLabel+"】？")){
		var json = {inId:inId,status:status,memo:$(".memo").val()} ;
		$.dataservice("model:Warehouse.In.doStatus",json,function(result){
			window.location.reload();
		});
	}
}

function productInWarehouse(){
	openCenterWindow("/saleProduct/index.php/page/forward/Warehouse.In.process/"+inId+"/"+status,860,630) ;
}

$(function(){
	var flowData = [
		{status:0,label:"编辑中",actions:[{label:"提交审批",action:function(){ AuditAction(10,"提交审批") }}]},
		{status:10,label:"待审批",actionLabel:"执行审批"
			,actions:[{label:"审批通过",action:function(){ AuditAction(20,"审批通过") } },
				{label:"审批不通过",action:function(){ AuditAction(0,"审批不通过") } }]},
		{status:20,label:"待发货"
			,actions:[{label:"发货完成",action:function(){ AuditAction(30,"发货完成") } }]},
		{status:30,label:"已发货"
			,actions:[{label:"到达海关",action:function(){ AuditAction(40,"到达海关") } }]},
		{status:40,label:"到达海关"
			,actions:[{label:"开始验货",action:function(){ AuditAction(50,"开始验货") } }]},
		{status:50,label:"验货中"
			,actions:[{label:"货品验收",action:function(){ productInWarehouse() ; } } ]},
		{status:60,label:"入库中"
			,actions:[
				{label:"货品入库",action:function(){ productInWarehouse() }   }
			]},
		{status:70,label:"入库完成"
			,actions:[{label:"查看入库货品",action:function(){ productInWarehouse();} } ]}
	] ;
	
	//初始化流程数据
	var flow = new Flow() ;
	flow.init(".flow-bar center",flowData) ;
	flow.draw(currentStatus) ;
	
	var tab = $('#details_tab').tabs( {
		tabs:[
			{label:'基本信息',iframe:true,url:"/saleProduct/index.php/page/model/Warehouse.In.edit/"+inId,id:'t1'},//9
			{label:'物流货品',iframe:true,url:"/saleProduct/index.php/page/model/Warehouse.In.editBox/"+inId,id:'t2'},
			{label:'跟踪状态',iframe:true,url:"/saleProduct/index.php/page/model/Warehouse.In.editTrack/"+inId,id:'t3'}
		] ,
		height:'520px',
		select:function(event,ui){
			var index = ui.index ;
			//renderAction(index);
		}
	} ) ;
	
	//
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

