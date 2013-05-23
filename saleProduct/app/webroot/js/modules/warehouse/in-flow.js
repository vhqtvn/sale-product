/**
 * 入库流程
 */
var  FlowFactory = {
		get: function( flag ,inSourceType ,permissions ){
			inSourceType = inSourceType||"out" ;
			var flowData =  flowConfig[flag] ||{} ;
			flowData = flowData[inSourceType]||{} ;
			$( flowData.flow ).each(function(){
				var permFlag = 'status_'+this.status ;
				var hasPerm = permFlag === false ?false : true ;
				if( hasPerm ) return ;
				this.actions = [] ;
			}) ;
			return flowData;
		}
}


var flowConfig = {
		/**
		 * 国内采购入库
		 */
		chinaLocal :{
			out: {//外部采购入库
				name:"中国本地采购入库",
				logistics: false, //是否物流
				flow:[ //流程数据
						{status:0,label:"编辑中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(0,"保存") }},
							          {label:"提交审批",action:function(){ AuditAction(10,"提交审批") }}]
						},
						{status:10,label:"待审批",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(10,"保存") }},
							          {label:"审批通过",action:function(){ AuditAction(50,"审批通过") } },
								{label:"审批不通过",action:function(){ AuditAction(0,"审批不通过") } }]
						},
						{status:50,label:"货品验收",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(50,"保存") }},
							          {label:"货品验收",action:function(){ productInWarehouse() ; } } ]
						},
						{status:60,label:"入库中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(60,"保存") }},
								{label:"货品入库",action:function(){ productInWarehouse() }   }
							]
						},
						{status:70,label:"入库完成"
							,actions:[
										{label:"导出装箱单",action:function(){ printBox();} },
										{label:"导出发票",action:function(){ printInvoice();} },
										{label:"查看入库货品",action:function(){ productInWarehouse();} } 
							]
						} 
				]
			},
			warehouse: {//转仓
				name:"中国本地采购入库",
				logistics: false, //是否物流
				flow:[ //流程数据
						{status:0,label:"编辑中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(0,"保存") }},
							          {label:"提交审批",action:function(){ AuditAction(10,"提交审批") }}]
						},
						{status:10,label:"待审批",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(10,"保存") }},
							          {label:"审批通过",action:function(){ AuditAction(50,"审批通过") } },
								{label:"审批不通过",action:function(){ AuditAction(0,"审批不通过") } }]
						},
						{status:15,label:"转仓出库",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(15,"保存") }},
								{label:"确认出库",action:function(){ transOutInventory(20,"确认出库" ) } }
							]
						},
						{status:20,label:"待发货",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(20,"保存") }},
								{label:"导出装箱单",action:function(){ printBox();} },
								{label:"导出发票",action:function(){ printInvoice();} },
								{label:"发货完成",action:function(){ AuditAction(30,"发货完成") } }
							]
						},
						{status:30,label:"已发货",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(30,"保存") }},
							   {label:"导出装箱单",action:function(){ printBox();} },
							   {label:"导出发票",action:function(){ printInvoice();} },
							   {label:"到达目标地",action:function(){ AuditAction(40,"到达目标地") } }
							]
						},
						{status:40,label:"到达目标地",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(40,"保存") }},
							          {label:"开始验货",action:function(){ AuditAction(50,"开始验货") } }]
						},
						{status:50,label:"货品验收",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(50,"保存") }},
							          {label:"货品验收",action:function(){ productInWarehouse() ; } } ]
						},
						{status:60,label:"入库中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(60,"保存") }},
								{label:"货品入库",action:function(){ productInWarehouse() }   }
							]
						},
						{status:70,label:"入库完成"
							,actions:[
										{label:"导出装箱单",action:function(){ printBox();} },
										{label:"导出发票",action:function(){ printInvoice();} },
										{label:"查看入库货品",action:function(){ productInWarehouse();} } 
							]
						} 
				]
			}
		},
		/**
		 * 中国到美国流程数据
		 */
		chinaToAmerican:{
			out: { //外部采购入库
				name:"中国到美国仓库",
				logistics:true,
				flow: [
						{status:0,label:"编辑中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(0,"保存") }},
							          {label:"提交审批",action:function(){ AuditAction(10,"提交审批") }}]
						},
						{status:10,label:"待审批",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(10,"保存") }},
							          {label:"审批通过",action:function(){ AuditAction(20,"审批通过") } },
								{label:"审批不通过",action:function(){ AuditAction(0,"审批不通过") } }]
						},
						{status:20,label:"待发货",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(20,"保存") }},
							          {label:"导出装箱单",action:function(){ printBox();} },
								{label:"导出发票",action:function(){ printInvoice();} },
								{label:"发货完成",action:function(){ AuditAction(30,"发货完成") } }
							]
						},
						{status:30,label:"已发货",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(30,"保存") }},
							          {label:"导出装箱单",action:function(){ printBox();} },
							   {label:"导出发票",action:function(){ printInvoice();} },
							   {label:"到达海关",action:function(){ AuditAction(40,"到达海关") } }
							]
						},
						{status:40,label:"到达海关",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(40,"保存") }},
							          {label:"开始验货",action:function(){ AuditAction(50,"开始验货") } }]
						},
						{status:50,label:"验货中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(50,"保存") }},
							          {label:"货品验收",action:function(){ productInWarehouse() ; } } ]
						},
						{status:60,label:"入库中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(60,"保存") }},
							          {label:"货品入库",action:function(){ productInWarehouse() }   }
							]
						},
						{status:70,label:"入库完成"
							,actions:[
										{label:"导出装箱单",action:function(){ printBox();} },
										{label:"导出发票",action:function(){ printInvoice();} },
										{label:"查看入库货品",action:function(){ productInWarehouse();} } 
							]
						}
					] 
			},
			warehouse: { //转仓
				name:"中国到美国仓库",
				logistics:true,
				flow: [
						{status:0,label:"编辑中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(0,"保存") }},
							          {label:"提交审批",action:function(){ AuditAction(10,"提交审批") }}]
						},
						{status:10,label:"待审批",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(10,"保存") }},
							          {label:"审批通过",action:function(){ AuditAction(15,"审批通过") } },
								{label:"审批不通过",action:function(){ AuditAction(0,"审批不通过") } }]
						},
						{status:15,label:"转仓出库",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(15,"保存") }},
							          {label:"确认出库",action:function(){ transOutInventory(20,"确认出库") } }
							]
						},
						{status:20,label:"待发货",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(20,"保存") }},
							          {label:"导出装箱单",action:function(){ printBox();} },
								{label:"导出发票",action:function(){ printInvoice();} },
								{label:"发货完成",action:function(){ AuditAction(30,"发货完成") } }
							]
						},
						{status:30,label:"已发货",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(30,"保存") }},
							          {label:"导出装箱单",action:function(){ printBox();} },
							   {label:"导出发票",action:function(){ printInvoice();} },
							   {label:"到达海关",action:function(){ AuditAction(40,"到达海关") } }
							]
						},
						{status:40,label:"到达海关",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(40,"保存") }},
							          {label:"开始验货",action:function(){ AuditAction(50,"开始验货") } }]
						},
						{status:50,label:"验货中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(50,"保存") }},
							          {label:"货品验收",action:function(){ productInWarehouse() ; } } ]
						},
						{status:60,label:"入库中",memo:true
							,actions:[
							          {label:"保存",action:function(){ AuditAction(60,"保存") }},
							          {label:"货品入库",action:function(){ productInWarehouse() }   }
							]
						},
						{status:70,label:"入库完成"
							,actions:[
										{label:"导出装箱单",action:function(){ printBox();} },
										{label:"导出发票",action:function(){ printInvoice();} },
										{label:"查看入库货品",action:function(){ productInWarehouse();} } 
							]
						}
					] 
			}
		}
} ;