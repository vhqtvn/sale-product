/**
 * 
 * author: lixh@bingosoft.net
 * 
 */
(function($) {
	var pluginName = "tree" ;
	
    $.fn.swapClass = function(c1, c2) {
        return this.removeClass(c1).addClass(c2);
    };
    $.fn.switchClass = function(c1, c2) {
        if (this.hasClass(c1)) {
            return this.swapClass(c1, c2);
        }
        else {
            return this.swapClass(c2, c1);
        }
    };
    $.fn.lighttree = function(settings,itemOrItemId,level,asyn) {
    	$(this).bind("contextmenu",function(e){
      	 	return false;
   		 }) ;
    	
    	if(settings.source == 'remote' || settings.source == null){
	    	settings.url = settings.url||(window.dataServiceUrl||"~/dataservice");
    	}
    	
        var dfop =
            {
            	source:'remote',
                method: "POST",
                dataType: "json",
                url: settings.url,
                path: '.' ,
                asyn: true ,//鏄惁寮傛鍔犺浇锛屽鏋滀负鍙跺瓙鑺傜偣
                //checkIcons: ["checkbox_0.gif", "checkbox_1.gif", "checkbox_2.gif"],
                checkIcons: ["bbit-tree-checkbox0", "bbit-tree-checkbox1", "bbit-tree-checkbox2"],
                showCheck: false, //鏄惁鏄剧ず閫夋嫨            
                onCheck: false, //褰揷heckstate鐘舵€佸彉鍖栨椂鎵€瑙﹀彂鐨勪簨浠讹紝浣嗘槸涓嶄細瑙﹀彂鍥犵骇鑱旈€夋嫨鑰屽紩璧风殑鍙樺寲
                onNodeClick: false,
                cascadeCheck: true, //'UP DOWN true(all) false'
                root:{value:'root',text:'鏍硅妭鐐�',icon:'bbit-tree-root'},
                data: null,
                clicktoggle: true, //鐐瑰嚮鑺傜偣灞曞紑鍜屾敹缂╁瓙鑺傜偣
                theme: "bbit-tree-arrows", //bbit-tree-lines ,bbit-tree-no-lines,bbit-tree-arrows
                //瀹氫箟榛樿鍥剧墖鍦板潃
                icons:{floderOpen:"bbit-tree-folderopen",leaf:"bbit-tree-leaf",floder:"bbit-tree-folder",loading:'bbit-tree-loading'},
                params : {},
                rootId:"root",
                rootText : false,
                isRootExpand:false,
                isTriState:false
            };
        
        //鎵╁睍灞炴€� 鐩稿root鍜宨cons鍗曠嫭杩涜璁剧疆锛屽惁鍒欏鑷村睘鎬т涪澶�
        if(settings && settings.root){
        	extendSubProp('root',['value','text','icon']) ;
        }
        
        if(settings && settings.icons){
        	extendSubProp('icons',['floderOpen','leaf','floder','loading']) ;
        }
        
        dfop.dataFormat = settings.dataFormat||function(data){return data;} ;
        dfop.nodeFormat = settings.nodeFormat||function(node){return node} ;
        
        $.extend(dfop, settings);

        //test data
        var count=0;
        
        //瀹氫箟鍐呴儴甯搁噺
        var _no_check  	= 0 ;
        var _yes_check 	= 1 ;
        var _hasf_check = dfop.isTriState?2:1 ;
        
        var me = $(this);
        var id = me.attr("id");
        if (id == null || id == "") {
            id = "bbtree" + new Date().getTime();
            me.attr("id", id);
        }

        var html = [];
        
        var treeData = dfop.data||( dfop.dataProxy?(dfop.dataProxy.type=='data'?dfop.dataProxy.value:null):null ) ;
        if(treeData == null )
        {
        	if(dfop.source == 'remote'){
        		var jsonStr = loadJsonTreeByURL(dfop.params,dfop.asyn,dfop.showCheck);
        		treeData = getJsonData(jsonStr);
        		$(treeData).each(function(){
        			//this.isExpand = true ;
        		}) ;
        	}
        	
        	if(dfop.rootText != false)
        	{
        		treeData = {
        			id:dfop.rootId,
        			text:dfop.rootText,
        			isExpand:true,
        			showCheck:false,
        			childNodes:treeData
        		};
        		//jsonStr = "{id:'"+ dfop.rootId + "',text:'" + dfop.rootText + "',isExpand:true,showCheck:false,childNodes:[" + jsonStr + "]}";
        	}
        	//treeData = eval('('+ jsonStr +')');
        }

        var treenodes = dfop.data = $.isArray(treeData)?treeData:[treeData] ;
        
        var __= dfop.data||( dfop.dataProxy?(dfop.dataProxy.type=='data'?dfop.dataProxy.value:null):null ) ;
        
        var treenodes = dfop.data = $.isArray(__)?__:[__] ;
        dfop.url =  dfop.url||( dfop.dataProxy?(dfop.dataProxy.type=='url'?dfop.dataProxy.value:null):null ) ;
        
        buildtree(dfop.data, html);
        me.addClass("ui-helper-reset bbit-tree").html(html.join(""));
       
        if(dfop.onLoaded){
          	dfop.onLoaded() ;
        }
        
        InitEvent(me);
        html = null;
        
		if(  dfop.expandLevel  && !dfop.asyn ){
			 setTimeout(function(){
				$(me).tree().expandLevel(null,dfop.expandLevel ) ;
			},0) ;
		}
		
		
		function __formatGridData(records){
			var array = [] ;
			var root = [] ;
			var map = {} ;
			var parentMap = {} ;
			$(records).each(function(){
				var row = {} ;
				for(var o in this){
					var _ = this[o] ;
					for(var o1 in _){
						row[o1] = _[o1] ;
						row[o1.toLowerCase()] = _[o1] ;
						if( o1.toLowerCase() == 'parent_id' ){
							row['parentId'] =  _[o1] ;
						}
					}
				}
				if(!row.parentId){
					root.push(row) ;
				}else{
					parentMap[row.parentId] = parentMap[row.parentId]||[] ;
					parentMap[row.parentId].push(row) ;
				}
				
				map[row['id']] = row ;
				//array.push(row) ;
			}) ;
			
			for(var o in map){
				var ps = parentMap[o] ;
				map[o].childNodes = ps ;
			}
			
			return root ;
		}

        //閫氳繃AJAX浠庡悗鍙板彇鏁版嵁
        function loadJsonTreeByURL(params,async,showCheck)
        {
            var tree = '';		
            
		  	asyn = asyn ? 'true' : 'false';
		  	showCheck = showCheck ? 'true' : 'false';
		  	
		  	params["showCheck"] = showCheck;
		  	params["async"] = async;
		  	
		  	var service_param = {CommandName:dfop.CommandName};
			service_param = $.extend(service_param, params);
			
			$.dataservice(dfop.CommandName,service_param,function (response){tree = response;},{async:false,url:dfop.url}) ;
			
			tree = __formatGridData(tree); 

		  	return tree;
  		}
        
        //鎵╁睍瀛愬睘鎬�
        function extendSubProp(sub,props){
        	$(props).each( function(){
        		if( settings[sub][this] == undefined ) settings[sub][this] = dfop[sub][this] ;
        	} ) ;
        }
       
        //region 
        function buildtree(data, ht) {
            ht.push("<div class='bbit-tree-bwrap'>"); // Wrap ;
            ht.push("<div class='bbit-tree-body'>"); // body ;
            
            //闇€瑕佹浛鎹㈠浘鐗囩殑鑺傜偣
           /* if( iconPath.indexOf(".") != -1 ){//缁濆璺緞
            	ht.push("<div class='bbit-tree-node-icon' style='background-image:url("+iconPath+")'></div>");
            }else{//鏍峰紡
            	ht.push("<div class='bbit-tree-node-icon "+iconPath+"'></div>");
            }*/
            
            ht.push("<ul class='bbit-tree-root ", dfop.theme, "'>"); //root
            if (data && data.length > 0) {
                var l = data.length;
                for (var i = 0; i < l; i++) {
                    buildnode(data[i], ht, 0, i, i == l - 1);
                }
                
                setTimeout(function(){
                	if(dfop.loadAfter){
			          	dfop.loadAfter() ;
			         }
                },10) ;
            }else {
                asnyloadc(null, false, function(data) {
            		data = getJsonData(data) ;
                    if ( data && data.length > 0 ) {
                        treenodes = data;
                        dfop.data = data;
                        var l = data.length;
                        for (var i = 0; i < l; i++) {
                            buildnode(data[i], ht, 0, i, i == l - 1);
                        }
                    }
                    setTimeout(function(){
	                	if(dfop.loadAfter){
				          	dfop.loadAfter() ;
				         }
	                },10) ;
                });
            }
            ht.push("</ul>"); // root and;
            ht.push("</div>"); // body end;
            ht.push("</div>"); // Wrap end;
            
        }
        
        //鑾峰彇鏍戣姹備互鍚庣殑瀵硅薄
        function getJsonData(data){
        
        	var ret = data ;
        	if(typeof data == 'string'){
        		eval( 'ret = '+data ) ;
        	}
        	
        	return dfop.dataFormat(ret) ;
        }
   
        //endregion
        function buildnode(nd, ht, deep, path, isend) {
        	var isRootExpand =  dfop.isRootExpand ;
 
        	var initDeep = isRootExpand?0:1 ;
        	nd = dfop.nodeFormat(nd) ;
            var nid = (nd.id+"");//.replace(/[^\w]/gi, "_");
            ht.push("<li class='bbit-tree-node ui-color-default'>");
            ht.push("<div id='", id, "_", nid, "' nodeid='",nid,"' tpath='", path, "' unselectable='on' title='", nd.text, "'");
            var cs = [];
            cs.push("bbit-tree-node-el");
            //鑷畾涔夊浘鏍�
            var _icon = nd.icon ;
            var _refIcon = nd.refIcon ;//open
            var iconPath = null ;
            var isExpandCss = null ;
            if (hasChildren(nd) ) {
            	isExpandCss = nd.isExpand ? "bbit-tree-node-expanded" : "bbit-tree-node-collapsed"
                cs.push(isExpandCss);
                iconPath = !nd.isExpand?(_icon?_icon:dfop.icons.floder):(_icon?_refIcon:dfop.icons.floderOpen) ;
            }else {
                cs.push("bbit-tree-node-leaf");
                iconPath = _icon?_icon:dfop.icons.leaf ;
            }
            
            //deep == 0 琛ㄧず涓烘牴鑺傜偣
            iconPath = deep == 0 ?(nd.icon||dfop.root.icon):iconPath;
            
            if (nd.classes) { cs.push(nd.classes); }

            ht.push(" class='", cs.join(" "), "'>");
            //span indent
            ht.push("<span class='bbit-tree-node-indent'>");
            
            if (deep == initDeep) {
            	//ht.push("<div class='bbit-tree-blank'></div>");
            }else if (deep >= initDeep+1) {
            	//ht.push("<div class='bbit-tree-blank'></div>");
                for (var j = initDeep; j < deep; j++) {
                    ht.push("<div class='bbit-tree-blank'></div>");
                }
            }
            
            ht.push("</span>");
            cs.length = 0;
      
            if (hasChildren(nd)) {
                if (nd.isExpand) {
                    cs.push(isend ? "bbit-tree-elbow-end-minus" : "bbit-tree-elbow-minus");
                }else {
                    cs.push(isend ? "bbit-tree-elbow-end-plus" : "bbit-tree-elbow-plus");
                }
            }else {
                cs.push(isend ? "bbit-tree-elbow-end" : "bbit-tree-elbow");
            }

            if(  deep>=initDeep ) {
            	ht.push("<div class='bbit-tree-ec-icon bbit-tree-blank ", cs.join(" "), "'></div>");
            }
         
            //闇€瑕佹浛鎹㈠浘鐗囩殑鑺傜偣
            if( iconPath.indexOf(".") != -1 ){//缁濆璺緞
            	ht.push("<div class='bbit-tree-node-icon' style='background-image:url("+$.utils.parseUrl(iconPath)+")'></div>");
            }else{//鏍峰紡
            	ht.push("<div class='bbit-tree-node-icon "+iconPath+"'></div>");
            }
            
            //checkbox
            if (isShowCheck(nd)) {
                if (nd.checkstate == null || nd.checkstate == undefined) {
                    nd.checkstate = _no_check;
                }
				var disableClz = (nd.disabled===true||nd.disabled===1)?' ui-state-disabled':'' ;
				
				ht.push("<div  id='", id, "_", nid, "_cb' class='bbit-tree-node-cb",disableClz," ",dfop.checkIcons[nd.checkstate],"'></div>");
              
              }
            
            //a
            ht.push("<a hideFocus class='bbit-tree-node-anchor' tabIndex=1 href='javascript:void(0);'>");
            ht.push("<span unselectable='on'>", nd.text, "</span>");
            ht.push("</a>");
            ht.push("</div>");
            
            //Child
            if ( hasChildren(nd) ) {
                if (nd.isExpand){
                    ht.push("<ul  class='bbit-tree-node-ct'  style='z-index: 0; position: static; visibility: visible; top: auto; left: auto;'>");
                    if (nd.childNodes) {
                        var l = nd.childNodes.length;
                        for (var k = 0; k < l; k++) {
                            nd.childNodes[k].parent = nd;
                            buildnode(nd.childNodes[k], ht, deep + 1, path + "." + k, k == l - 1);
                        }
                    }
                    ht.push("</ul>");
                }else {
                    ht.push("<ul style='display:none;'></ul>");
                }
            }
            ht.push("</li>");
            nd.render = true;
            cs.length = 0 ;
        }
        
        function hasChildren(nd){
        	return nd.hasChildren|| 
        		( false == nd.complete && typeof(nd.hasChildren)=='undefined'  )||
        		(nd.childNodes && nd.childNodes.length>0  )
        }
        
        function isComplete(nd){//鏄惁宸茬粡鍔犺浇瀹屾垚
        	if( nd.hasChildren && !nd.childNodes ){
        		nd.complete = false ;
        	}else if(typeof(nd.complete) == 'undefined'){
        		nd.complete = true ;
        	}
        	return nd.complete ;
        }
        
        function isLeaf(item){
        	if(typeof(item) == 'string'){//item id
	        		item = getItemById(item) ;
	        }
        	return !( item.complete === false || ( item.childNodes && item.childNodes.length > 0 ) ) ;
        }
        
        function getItem(node) {
        	var nodeid =node.attr("nodeid");

        	var sn =  searchNode(nodeid)||{};
        	
        	return sn.node ;
        	/*
        	//nodeid
        	try{
	            var ap = path.split(".");
	            var t = treenodes;
	            for (var i = 0; i < ap.length; i++) {
	            	t = i==0?t[ap[i]]:t.childNodes[ap[i]];
	            }
	            return t;
        	}catch(e){
        	}*/
        }
        
        function checkAll(state){//treenodes
        	for( var i=0 ;i < treenodes.length ;i++){ //鍒犻櫎鏁版嵁
        		__search( treenodes[i]) ;
        		check(treenodes[i],state,1) ;
        		
            }
            return null ;
            
            function __search( node ){
            	if ( node.childNodes && node.childNodes.length > 0) {
                 	  for(var j=0 ;j<node.childNodes.length ;j++){
                 	  	    __search( node.childNodes[j]) ;
                 	  		check(node.childNodes[j],state,1) ;
                 	  }
                }
            }
        }
        
        /**
         * 澶嶉€夋閫夋嫨浜嬩欢
         * item 锛氭暟鎹」
         * state锛氶紶鏍囩偣鍑昏妭鐐圭姸鎬�
         * type: 0-閬嶅巻鐖惰妭鐐�  1-閬嶅巻瀛愯妭鐐�
         */
        function check(item, state, type) {
			var nid = item.id;//.replace(/[^\w]/gi, "_");
            var et = $("#" + id + "_" + nid + "_cb");
            if (et.length == 1 && et.hasClass('ui-state-disabled')) {
                return ;
            }

            var pstate = item.checkstate;
            if (type == 1) {
                item.checkstate = state;
            } else {// 涓婃函
                var cs = item.childNodes;
                var l = cs.length;
                var ch = true;
                for (var i = 0; i < l; i++) {
                    if ((state == _yes_check && cs[i].checkstate != _yes_check) || state == _no_check && cs[i].checkstate != _no_check) {
                        ch = false;
                        break;
                    }
                }
                item.checkstate = ch?state:_hasf_check ;
            }
            
            if(pstate != item.checkstate){
            	if( dfop.onChecking ){
		        	if( dfop.onChecking( item.id , item.text ,!(pstate==1?true:false),item  ) === false ) {
		        		item.checkstate = pstate ;//杩樺師鐘舵€�
		        		return ;
		        	};
	        	}
            }
            
            //change show
            if (item.render && pstate != item.checkstate) {
            	// 濡傛灉鍚戜笂閬嶅巻 && 褰撳墠榧犳爣鐐瑰嚮鑺傜偣娌℃湁閫変腑 && 褰撳墠鍙栨秷閫夋嫨鐖惰妭鐐�,鍒欑埗鑺傜偣浠嶇劧淇濇寔閫変腑鐘舵€�
            	if( ( !dfop.isTriState ) && type == 0 && state == _no_check &&  item.checkstate == _no_check ){
            		item.checkstate = _hasf_check;
            	}
            	
                if (et.length == 1) {
                	et.removeClass().addClass("bbit-tree-node-cb "+dfop.checkIcons[item.checkstate]) ;
                }
            }

			if( pstate != item.checkstate ){
	        	if( dfop.onChecked ){
		        	dfop.onChecked( item.id , item.text ,(item.checkstate==1?true:false),item  )
	        	}
            }
        }
        //閬嶅巻瀛愯妭鐐�
        function cascade(fn, item, args) {
            if (fn(item, args, 1) != false) {
                if (item.childNodes != null && item.childNodes.length > 0) {
                    var cs = item.childNodes;
                    for (var i = 0, len = cs.length; i < len; i++) {
                        cascade(fn, cs[i], args);
                    }
                }
            }
        }
        //鍐掓场鐨勭鍏�
        function bubble(fn, item, args) {
            var p = item.parent;
            while (p) {
                if (fn(p, args, 0) === false) {
                    break;
                }
                p = p.parent;
            }
        }
        
        function setNodeIcon(nd,flag){
        	var _icon = nd.icon ;
            var _refIcon = nd.refIcon ;//open
            var iconPath = null ;
            
            if( $(this).hasClass("bbit-tree-node-expanded") ){
            	iconPath = _icon?_icon:dfop.icons.floderOpen ;
            }else if( $(this).hasClass("bbit-tree-node-collapsed") ){
            	iconPath = _icon?_refIcon:dfop.icons.floder ;
            }
            if(iconPath && !$(this).hasClass('ui-state-highlight')){
            	$(this).find('.bbit-tree-node-icon').removeClass().addClass('bbit-tree-node-icon '+iconPath) ;
            }
        }
        //add Param: level and asyn isRoot(first load)
        function _expandNode(e , et ,item ,path , level , asyn,isRoot){
        	var ul = $(this).next(); //"bbit-tree-node-ct"
        	
        	if( dfop.onExpand ){ //鑺傜偣灞曞紑浜嬩欢
        		dfop.onExpand( item.id , item, ul.hasClass("bbit-tree-node-ct") , isComplete(item) ) ;
        	}

            if (ul.hasClass("bbit-tree-node-ct")) {
                ul.show();
            }else {
                var deep = path.split(".").length;
                if ( isComplete(item) ) {//琛ㄧず瀛楄妭鐐瑰姞杞藉畬鎴�
                    item.childNodes != null && asnybuild(item.childNodes, deep, path, ul, item);
                }else {
                    $(this).addClass("ui-state-highlight");
                    $(this).find('.bbit-tree-node-icon').removeClass().addClass('bbit-tree-node-icon '+dfop.icons.loading);
                    asnyloadc(item, true, function(data) {
                    	data = getJsonData(data) ;
                    	
                    	var array = [] ;
            			$(data).each(function(){
            				var row = {} ;
            				for(var o in this){
            					var _ = this[o] ;
            					for(var o1 in _){
            						row[o1] = _[o1] ;
            						row[o1.toLowerCase()] = _[o1] ;
            						if( o1.toLowerCase() == 'parent_id' ){
            							row['parentId'] =  _[o1] ;
            						}
            					}
            				}
            				array.push(row) ;
            			}) ;
            			data = array ;
            			
                    	//data = __formatGridData(data) ;
                        item.complete = true;
                        item.childNodes = data;
                        asnybuild(data, deep, path, ul, item);
                        //if level>=0 灞曞紑璇ヨ妭鐐�
                        if( (level>=1 && level<100) || isRoot ) expandAll(item , asyn ,level ) ;
                    });
                }
            }
            
            $(this).swapClass("bbit-tree-node-collapsed", "bbit-tree-node-expanded");

            if ($(et).hasClass("bbit-tree-elbow-plus")) {
                $(et).swapClass("bbit-tree-elbow-plus", "bbit-tree-elbow-minus");
            }else if ($(et).hasClass("bbit-tree-elbow-end-plus")){
                $(et).swapClass("bbit-tree-elbow-end-plus", "bbit-tree-elbow-end-minus");
            }
            //鍥炬爣杞崲
            setNodeIcon.call(this,item,1) ;
            
        }
        
        function _collNode(e,et,item){
        	var me = this ;
        	if(!et){
        		if(typeof(item) == 'string'){//item id
	        		item = getItemById(item) ;
	        	}
	            var nid = item.id;//.replace(/[^\w]/gi, "_");
	            var div = $("#" + id + "_" + nid + " div.bbit-tree-ec-icon");
	            et = div ;
	            me = div.parent() ;
        	}
        	
	        $(me).next().hide();
                	
            $(me).swapClass("bbit-tree-node-expanded", "bbit-tree-node-collapsed");
            if ($(et).hasClass("bbit-tree-elbow-minus")) {
                $(et).swapClass("bbit-tree-elbow-minus", "bbit-tree-elbow-plus");
            }else if ($(et).hasClass("bbit-tree-elbow-end-minus")){
                $(et).swapClass("bbit-tree-elbow-end-minus", "bbit-tree-elbow-end-plus");
            }
            //鍥炬爣杞崲
            setNodeIcon.call(me,item,2) ;
        }
        
        /**
         * 鑺傜偣鍗曞嚮浜嬩欢
         */
        function nodeClick(e) {
            var path = $(this).attr("tpath");
            var et = e.target || e.srcElement;
            
            var item = getItem( $(this) );
            
            if (et.tagName == "DIV") {
            	if( $(et).hasClass("bbit-tree-node-icon")  ){
            		et = $(et).prev(); 
            	}
                // +鍙烽渶瑕佸睍寮€ "bbit-tree-node-expanded" : "bbit-tree-node-collapsed"
                if ($(et).hasClass("bbit-tree-elbow-plus") || $(et).hasClass("bbit-tree-elbow-end-plus")) {
                	_expandNode.call( this ,e, et , item,path ) ;
                }else if ($(et).hasClass("bbit-tree-elbow-minus") || $(et).hasClass("bbit-tree-elbow-end-minus")) {  //- 鍙烽渶瑕佹敹缂�                    
                	_collNode.call(this , e , et , item) ;
              }else if ($(et).hasClass("bbit-tree-node-cb") && !$(et).hasClass('ui-state-disabled')) // 鐐瑰嚮浜咰heckbox
                {
                    var s = item.checkstate != _yes_check ? _yes_check : _no_check;
                    var r = true;
                    
                    if (r != false) {
                        if (dfop.cascadeCheck) {
                        	var self = false ;
                        	if( 'DOWN' == dfop.cascadeCheck || dfop.cascadeCheck === true ){
                        		self = true ;
                        		//鍚戜笅閬嶅巻
                            	cascade(check, item, s);
                        	}
                        	
                        	if(!self)check(item, s, 1);//閫変腑鑷繁
                        	
                        	if( 'UP' == dfop.cascadeCheck || dfop.cascadeCheck === true ){
                        		//鍚戜笂婧�
                            	bubble(check, item, s);
                        	}
                        	
                        }else {
                            check(item, s, 1);
                        }
                    }
                    
                    if (dfop.onCheck) {
                    	var _a = typeof(dfop.onCheck) == 'string'?eval(dfop.onCheck):dfop.onCheck ;
                    	r = _a.call(et,item.id,item.text,!(item.checkstate==1?true:false),item,et);
                    }
                }
            }else if(et.tagName == 'SPAN'){
                if (dfop.citem){
                    var nid = dfop.citem.id;//.replace(/[^\w]/gi, "_");
                    $("#" + id + "_" + nid).removeClass("bbit-tree-selected ui-state-active active");
                }
                dfop.citem = item;
                $(this).addClass("bbit-tree-selected ui-state-active active");

                if (dfop.onNodeClick){
                    if (!item.expand){
                        item.expand = function() { expandnode.call(item); };
                    }
                    var _a = typeof( dfop.onNodeClick ) == 'string'?eval(dfop.onNodeClick):dfop.onNodeClick ;
	                _a.call(this, item.id , item.text , item , this);
                }
           }
        }
        
        /**
         * 鍙屽嚮浜嬩欢,鎵撳紑鍚堝苟鑿滃崟
         */
        function nodeDblClick(e){
        	var path = $(this).attr("tpath");
            var et = e.target || e.srcElement;
            var item = getItem( $(this) ) ;
            if(et.tagName == 'SPAN'){
            	//鑾峰彇鎸囧畾鐨勫璞�
            	et = $(this).find('.bbit-tree-node-icon').prev() ;
                if ($(et).hasClass("bbit-tree-elbow-plus") || $(et).hasClass("bbit-tree-elbow-end-plus")) {
                    var ul = $(this).next(); //"bbit-tree-node-ct"
                    if (ul.hasClass("bbit-tree-node-ct")) {
                        ul.show();
                    }else {
                        var deep = path.split(".").length;
                        if (isComplete(item)) {
                            item.childNodes != null && asnybuild(item.childNodes, deep, path, ul, item);
                        }else {
                            $(this).addClass("ui-state-highlight");
                            asnyloadc(item, true, function(data) {
                            	data = getJsonData(data) ;
                                item.complete = true;
                                item.childNodes = data;
                                asnybuild(data, deep, path, ul, item);
                            });
                        }
                    }
                    
                    $(this).swapClass("bbit-tree-node-collapsed", "bbit-tree-node-expanded");
                    if ($(et).hasClass("bbit-tree-elbow-plus")) {
                        $(et).swapClass("bbit-tree-elbow-plus", "bbit-tree-elbow-minus");
                    }else {
                        $(et).swapClass("bbit-tree-elbow-end-plus", "bbit-tree-elbow-end-minus");
                    }
                    setNodeIcon.call(this,item,1) ;
                }else if ($(et).hasClass("bbit-tree-elbow-minus") || $(et).hasClass("bbit-tree-elbow-end-minus") ) {  //- 鍙烽渶瑕佹敹缂�                    
                	$(this).next().hide();
                	
                    $(this).swapClass("bbit-tree-node-expanded", "bbit-tree-node-collapsed");
                    if ($(et).hasClass("bbit-tree-elbow-minus")) {
                        $(et).swapClass("bbit-tree-elbow-minus", "bbit-tree-elbow-plus");
                    }else {
                        $(et).swapClass("bbit-tree-elbow-end-minus", "bbit-tree-elbow-end-plus");
                    }
                    setNodeIcon.call(this,item,1) ;
                }
           }
        }
        
        function asnybuild(nodes, deep, path, ul, pnode,dyn) {
        	if( nodes && nodes.length > 0){
        		var l = nodes.length;
	            if (l > 0) {
	                var ht = [];
	                var ids = [] ;
	                
	                //鑾峰彇鍘熸潵鏈夊灏戜釜鑺傜偣
	                var base =  ul.children('li').length  ;
	                
	                for (var i = 0; i < l; i++) {
	                	ids.push('#'+id+"_"+nodes[i].id) ;
	                    nodes[i].parent = pnode;
	                    buildnode(nodes[i], ht, deep, path + "." + (base+i), i == l - 1);
	                }
	                
	                if(dyn){
	                	ul.append(ht.join(""));
	                	$(ids.join(',')).each(bindevent);
	                }else{
	                	ul.html(ht.join(""));
	                	InitEvent(ul);
	                }
	                //if(dfop.onLoaded) dfop.onLoaded() ;
	                ht = null;
	            }
	            ul.addClass("bbit-tree-node-ct").css({ "z-index": 0, position: "static", visibility: "visible", top: "auto", left: "auto", display: "" });
	       		ul.prev().find('.bbit-tree-node-icon').removeClass().addClass("bbit-tree-node-icon "+ (pnode.icon||dfop.icons.floderOpen) ) ;//
	       		//濡傛灉鍘熸潵鏄彾瀛愯妭鐐癸紝闇€瑕佷慨鏀�
	       		ul.prev().find('.bbit-tree-ec-icon').removeClass().addClass('bbit-tree-ec-icon bbit-tree-elbow-minus') ;
	       }else{//娌℃湁鑺傜偣
        		//鏇挎崲鏍峰紡
        		var et = ul.prev().find('.bbit-tree-ec-icon') ;
        		if( et.hasClass("bbit-tree-elbow-end-minus") ){
        			et.removeClass().addClass('bbit-tree-ec-icon bbit-tree-elbow-end') ;
        		}else{
        			et.removeClass().addClass('bbit-tree-ec-icon bbit-tree-elbow') ;
        		}
        		//鏇挎崲鍥剧墖,璁剧疆涓哄彾瀛愯妭鐐圭殑鍥剧墖
        		ul.prev().find('.bbit-tree-node-icon').removeClass().addClass('bbit-tree-node-icon '+( pnode.icon||dfop.icons.leaf )) ;
        	}   
        	ul.prev().removeClass("ui-state-highlight");
        }
        
        function asnyloadc(pnode, isAsync, callback) {
            if ( dfop.url ) {
            	//鏋勯€犺皟鐢ㄥ弬鏁�
                var param = builparam(pnode);
                if(param != null){
	                dfop.params["parentId"] = param.parentId;
	                dfop.params["checkState"] = param.checkState;
                }
                
                var service_param = $.extend({CommandName:dfop.CommandName}, dfop.params);
				
				if(dfop.params["childSqlId"]){
					service_param["sqlId"] = dfop.params["childSqlId"];
				}
                
				$.dataservice(dfop.CommandName,service_param, callback ,{async:false,url:dfop.url}) ;
            }
        }
        
        //鏋勯€犲弬鏁�
        function builparam(node) {
        	var param = dfop.dataProxy?dfop.dataProxy.params:{} ;
        	if (node && node != null){
        		$.extend(param, {
        			parentId:encodeURIComponent(node.id),
        			checkState:node.checkstate
        		});
        	}
            return param;
        }
        
        //缁戝畾浜嬩欢鍒拌妭鐐�
        function bindevent() {
            $(this).hover(function() {
                $(this).addClass("bbit-tree-node-over ui-state-hover");
            }, function() {
                $(this).removeClass("bbit-tree-node-over ui-state-hover");
            }).click(nodeClick).dblclick(nodeDblClick)
             .find("div.bbit-tree-ec-icon").each(function(e) {
                 if (!$(this).hasClass("bbit-tree-elbow")) {
                     $(this).hover(function() {
                         $(this).parent().addClass("bbit-tree-ec-over");
                     }, function() {
                         $(this).parent().removeClass("bbit-tree-ec-over");
                     });
                 }
             }) ;
             
             if(dfop.contextMenu){//濡傛灉瀛樺湪鍙抽敭鑿滃崟
            	 $(this).bind('contextmenu', function(e){
            		 _contextMenu.call(this,e) ;
            	 });
             }
        }
        
        function _contextMenu(e){
        	var nid = $(this).attr('nodeid') ;
       	    var item = getItemById(nid) ;
       	    var options = dfop.contextMenu(item) ;
       	    if(options["items"].length == 0) return;
        	options.eventType = 'contextmenu' ;
        	$(this).contextmenu(options) ;
        	$(this).contextmenu(options).show(e) ;
        }
        
        //鍒濆鍖栦簨浠�
        function InitEvent(parent) {
            var nodes = $("li.bbit-tree-node>div", parent);
            nodes.each(bindevent);
        }
         
        function expandAll(_item , asyn ,level , isRoot){//灞曞紑鎵€鏈夎妭鐐�
        	if(!_item){
        		$( treenodes ).each(function(index,item){
        			expandAll(item,asyn ,level , isRoot) ;
        		});
        		return ;
        	}
        	
        	if( level === 0 ) return ;
        	level = parseInt(level||10000) ;
        	
        	if(typeof(_item) == 'string'){//item id
        		_item = getItemById(_item) ;
        	}
        	var item = _item||this;
        	if(!_item){
        		//alert(item.id)
        	}
        	
            var nid = item.id;//.replace(/[^\w]/gi, "_");
            var div = $("#" + id + "_" + nid + " div.bbit-tree-ec-icon");
            
            if (div.length > 0) {
            	var path = div.parent().attr("tpath");
            	//闈炲彾瀛愯妭鐐�
            	if( !isLeaf(item) )
            		_expandNode.call( div.parent(), null,div,item,path,level,asyn,isRoot) ;
	        }
	        level-- ;
            
        	//灞曞紑鎵€鏈夎妭鐐�
        	$(item.childNodes).each(function(){
        		var _ = this ;
	            var nid = _.id;//.replace(/[^\w]/gi, "_");
	            var div = $("#" + id + "_" + nid + " div.bbit-tree-ec-icon");
	            if( !asyn &&  _.complete === false && level > 100 ) return ; 
	            expandAll( _ , asyn , level ) ;
        	}) ;
        }
        
        function expandnode(_item) {
            var item = _item||this;
            var nid = item.id;//.replace(/[^\w]/gi, "_");
            var div = $("#" + id + "_" + nid + " div.bbit-tree-ec-icon");
            if (div.length > 0) {
                div.click();
            }
        }
        
        function getItemById(itemId){//鑾峰彇item閫氳繃ID
        	var nid = itemId;//.replace(/[^\w-]/gi, "_");
            var node = $("#" + id + "_" + nid);
            if (node.length > 0) {
                var path = node.attr("tpath");
                var item = getItem(node);
                return item ;
            }
            return null;
        }
        
        function refresh(itemId) {
            var nid = itemId;//.replace(/[^\w-]/gi, "_");
            var node = $("#" + id + "_" + nid);
            if (node.length > 0) {
                node.addClass("ui-state-highlight");
                var isend = node.hasClass("bbit-tree-elbow-end") || node.hasClass("bbit-tree-elbow-end-plus") || node.hasClass("bbit-tree-elbow-end-minus");
                var path = node.attr("tpath");
                var deep = path.split(".").length;
                var item = getItem(node);
                if (item) {
                    asnyloadc(item, true, function(data) {
                    	data = getJsonData(data) ;
                        item.complete = true;
                        item.childNodes = data;
                        item.isExpand = true;
                        if (data && data.length > 0) {
                            item.hasChildren = true;
                        }else {
                            item.hasChildren = false;
                        }
                        var ht = [];
                        buildnode(item, ht, deep - 1, path, isend);
                        ht.shift();
                        ht.pop();
                        var li = node.parent();
                        li.html(ht.join(""));
                        ht = null;
                        InitEvent(li);
                        bindevent.call(li.find(">div"));
                        //if(dfop.onLoaded) dfop.onLoaded() ;
                    });
                }
            }else {
                alert("璇ヨ妭鐐硅繕娌℃湁鐢熸垚");
            }
        }
        
        function isShowCheck(nd){
        	if( typeof nd.showCheck == 'undefined' )
        		nd.showCheck = true ;
        	return dfop.showCheck && nd.showCheck ;
        }
        var count = 0;
        
        /**
         * 鑾峰彇鑺傜偣items閫変腑鍊�
         */
        function getck(items, c, fn) {
            for (var i = 0, l = items.length; i < l; i++) {
                ( isShowCheck(items[i]) &&  items[i].checkstate == _yes_check ) && c.push(fn(items[i]));
                if (items[i].childNodes != null && items[i].childNodes.length > 0) {
                    getck(items[i].childNodes, c, fn);
                }
            }
        }
        function getCkAndHalfCk(items, c, fn) {
            for (var i = 0, l = items.length; i < l; i++) {
                (isShowCheck(items[i]) && (items[i].checkstate == _yes_check || items[i].checkstate == _hasf_check)) && c.push(fn(items[i]));
                if (items[i].childNodes != null && items[i].childNodes.length > 0) {
                    getCkAndHalfCk(items[i].childNodes, c, fn);
                }
            }
        }
        
        function itemClone(item){
        	if(!item) return null ;
        	var it = {} ;
        	it 				= $.extend(it,item) ;
        	it.childNodes 	= undefined ;
        	it.parent 		= undefined ;
        	it.render 		= undefined ;
        	it.complete 	= undefined ;
        	it.showCheck 	= undefined;
        	it.hasChildren	= undefined ;
        	it.isExpand		= undefined ;
        	return it ;
        }
        
         function disabled(item,bool){
        	if(dfop.onDisabling){
        		if( dfop.onDisabling(item,bool) === false ) return ;
        	}
        	item = typeof(item) == 'string'?getItemById(item) : item ;
        	var nid = item.id;//.replace(/[^\w]/gi, "_");
            var div = $("#" + id + "_" + nid + " div.bbit-tree-node-cb");
            if(false === bool){
            	div.removeClass('ui-state-disabled') ;
            }else{
            	if(!div.hasClass('ui-state-disabled'))div.addClass('ui-state-disabled') ;
            }
            if(dfop.onDisabled){
            	dfop.onDisabled(item,bool)
        	}
        }
        
        function searchNode(item){//鑺傜偣鎼滅储
        	var _id = typeof(item)=='string'?item:item.id ;
    
        	for( var i=0 ;i < treenodes.length ;i++){ //鍒犻櫎鏁版嵁
        		var s = __search(_id , treenodes[i] , treenodes , i , null) ;
            	if( s ) return s;
            }
            return null ;
            
            function __search(id , node , treenodes , index , pnode){
            	if( id == node.id ){
            		return {id:id,node:node,array:treenodes,index:index,pnode:pnode} ;
            	}
            	if ( node.childNodes != null && node.childNodes.length > 0) {
                 	  for(var j=0 ;j<node.childNodes.length ;j++){
                 	  	var s = __search( id , node.childNodes[j],node.childNodes,j,node) ;
                 	  	if(s){
                 	  		return s;
                 	  	}
                 	  }
                }
                return false ;
            }
        }
        
        me[0].t = {
            getSelectedNodes: function(gethalfchecknode) {
                var s = [];
                if (gethalfchecknode) {
                    getCkAndHalfCk(treenodes, s, function(item) { return itemClone(item); });
                }else {
                    getck(treenodes, s, function(item) { return itemClone(item); });
                }
                return s;
            },getSelectedValues: function(gethalfchecknode) {
                var s = [];
                if(gethalfchecknode){
                	getCkAndHalfCk(treenodes, s, function(item) { return item.value||item.id ; });
                }else{
                	getck(treenodes, s, function(item) { return item.value||item.id; });
                }
                return s;
            },
            getCurrentItem: function() {
                return itemClone( dfop.citem );
            },
            refresh: function(itemOrItemId) {
                var id;
                if (typeof (itemOrItemId) == "string") {
                    id = itemOrItemId;
                }else {
                    id = itemOrItemId.id;
                }
                refresh(id);
            },
            expandAll:function(item,asyn,level){
            	expandAll(item,asyn,level,true) ;
            },
            checkAll:function(state){
            	checkAll(state) ;
            },
            collapse :function(item){
            	_collNode(null,null,item) ;
            },isLeaf:function(item){
            	return isLeaf(item) ;
            },treeOption:function(option,value){
            	if(value==undefined) return dfop[option] ;
            	dfop[option] = value ;
            },deleteNode:function(item){
            	var _id = typeof(item)=='string'?item:item.id ;
            	var sn = searchNode(_id) ;
            	if(dfop.onDeleting) {
            		if( dfop.onDeleting( _id , sn.node )=== false ) return  ;
            	}
            	
	            var nid = _id;//.replace(/[^\w]/gi, "_");
	            $("#" + id + "_" + nid).next("ul").remove();
	            $("#" + id + "_" + nid).parent().remove() ; //鍒犻櫎DOM鑺傜偣
	            
	            if(sn){
	            	//
	            	if( dfop.citem && dfop.citem.id == sn.node.id ) dfop.citem = null ;
	            	
	            	sn.array.splice(sn.index,1) ;
	            	if( (!sn.array||sn.array.length<=0) && sn.pnode){//濡傛灉鑺傜偣涓嶅瓨鍦紝闇€瑕佸皢鐖惰妭鐐硅浆鎹�
	            		var parentId = sn.pnode.id ;
	            		var nid = parentId;//.replace(/[^\w-]/gi, "_");
	            		var ul = $("#" + id + "_" + nid).next() ;
	            		var et = ul.prev().find('.bbit-tree-ec-icon') ;
		        		if( et.hasClass("bbit-tree-elbow-end-minus") ){
		        			et.removeClass().addClass('bbit-tree-ec-icon bbit-tree-elbow-end') ;
		        		}else{
		        			et.removeClass().addClass('bbit-tree-ec-icon bbit-tree-elbow') ;
		        		}
		        		//鏇挎崲鍥剧墖,璁剧疆涓哄彾瀛愯妭鐐圭殑鍥剧墖
		        		ul.prev().find('.bbit-tree-node-icon').removeClass().addClass('bbit-tree-node-icon '+(sn.pnode.icon||dfop.icons.leaf )) ;
	            	}
	            }
	            
	            if(dfop.onDeleted) {
            		 dfop.onDeleted( _id , getItemById(_id) )  ;
            	}
            },updateNode:function(item){
            	var _id   = item.id ;
            	if(dfop.onUpdating) {
            		if( dfop.onUpdating(_id, item )=== false ) return  ;
            	}
            	
            	var text = item.text ;
	            var nid = _id;//.replace(/[^\w]/gi, "_");
	            $("#" + id + "_" + nid).find('a span').html(text) ;
	            
	            var sn = searchNode(_id) ;
	            if(sn){
	            	sn.node	= $.extend(sn.node,item);
	            	sn.node.text = text ;
	            	if( dfop.citem && dfop.citem.id == sn.node.id ) dfop.citem = sn.node ;
	            }
	            if(dfop.onUpdated) {
            		 dfop.onUpdated( _id  ,item)  ;
            	}
            },addNode:function(item){
            	var _id = item.id ;
            	if(dfop.onAdding) {
            		if( dfop.onAdding( _id , item )=== false ) return  ;
            	}
	           
	            var parentId = item.parentId ;
	            var text = item.text ;
	            var pnode = getItemById(parentId) ;
	            var nodes = $.isArray(item)?item:[item] ;
	            
	            var nid = parentId;//.replace(/[^\w-]/gi, "_");
	            var path = $("#" + id + "_" + nid).attr("tpath");
	            var deep = path.split(".").length;
	            
	            //濡傛灉鐖惰妭鐐规病鏈夊睍寮€锛岄渶瑕佸睍寮€鐖惰妭鐐�
	            if(!pnode.isExpand){
	            	expandnode(pnode) ;
	            }
	            
	            var ul = $("#" + id + "_" + nid).next() ;
	            if( !ul.get(0) ){//濡傛灉ul涓嶅瓨鍦紝鍒欓渶瑕佹坊鍔犱竴涓猽l鑺傜偣
	            	$("#" + id + "_" + nid).parent().append("<ul style='display:none;'></ul>") ;
	            	ul = $("#" + id + "_" + nid).next() ;
	            }
	            
	            var sn = searchNode(parentId) ;
	            if(sn){
	            	sn.node.childNodes = sn.node.childNodes||[] ;
	            	sn.node.childNodes.push(item) ;
	            }
	            
	            asnybuild( nodes , deep ,path , ul , pnode,true ) ;
	            
	            if(dfop.onAdded) {
            		 dfop.onAdded( _id , item)  ;
            	}
            },checkNode:function(item , state){
            	var temp = item ;
            	item = typeof(item) == 'string'?getItemById(item) : item ;
            	
            	if( !item ){
            		item = searchNode(temp)  ;
            		item && ( item.node.checkstate = (state===true||state===1)?1:0 );
            	}else{
            		item.checkstate = (state===true||state===1)?0:1 ;
            		var nid = item.id;//.replace(/[^\w]/gi, "_");
	            	var div = $("#" + id + "_" + nid + " div.bbit-tree-node-cb");
	            	div.click() ;
            	}
            	return item ;
            	
            },disableNode:function(item,bool){
            	disabled(item, bool) ;
            }
        };
        return me;
    };
    
    $.extend($.fn , {
    	getSelectedIds :function() { //鑾峰彇鎵€鏈夐€変腑鐨勮妭鐐圭殑Value鏁扮粍
	        if (this[0].t) {
	            return this[0].t.getSelectedValues();
	        }
	        return null;
	    },getSelectNodes:function(gethalfchecknode) {//鑾峰彇鎵€鏈夐€変腑鐨勮妭鐐圭殑Item鏁扮粍
	        if (this[0].t) {
	            return this[0].t.getSelectedNodes(gethalfchecknode);
	        }
	        return null;
	    },getCurrentNode : function() {
	        if (this[0].t) {
	            return this[0].t.getCurrentItem();
	        }
	        return null;
	    },refresh : function(ItemOrItemId) {
	        if (this[0].t) {
	            return this[0].t.refresh(ItemOrItemId);
	        }
	    },expandAll: function(item,asyn,event){//item闇€瑕佸睍寮€鐨勮妭鐐癸紝榛樿鏍硅妭鐐癸紝asyn鏃跺€欏睍寮€寮傛鑺傜偣
	    	if (this[0].t) {
	            return this[0].t.expandAll(item,asyn,null);
	        }
	    },collapse : function(item){
	    	if (this[0].t) {
	            return this[0].t.collapse(item);
	        }
	    },expandLevel:function(item,level){//item闇€瑕佸睍寮€鐨勮妭鐐癸紝榛樿鏍硅妭鐐癸紝level灞傛
	    	if (this[0].t) {
	            return this[0].t.expandAll(item,true,level);
	        }
	    },isLeaf :function(item){
	    	if (this[0].t) {
	            return this[0].t.isLeaf(item);
	        }
	    },treeOption:function(option,value){//鑾峰彇灞炴€ф垨璁剧疆灞炴€�
	    	if (this[0].t) {
	            return this[0].t.treeOption(option,value);
	        }
	    },deleteNode:function(item){
	    	if (this[0].t) {
	            return this[0].t.deleteNode(item);
	        }
	    },updateNode:function( item ){
	    	if (this[0].t) {
	            return this[0].t.updateNode(item);
	        }
	    },addNode:function( item ){
	    	if (this[0].t) {
	            return this[0].t.addNode(item);
	        }
	    },checkAll:function( state ){
	    	if (this[0].t) {
	            return this[0].t.checkAll(state);
	        }
	    },checkNode:function(item,state){
	    	if (this[0].t) {
	            return this[0].t.checkNode(item,state);
	        }
	    },disableNode:function(item,bool){
	    	if (this[0].t) {
	            return this[0].t.disableNode(item,bool);
	        }
	    }
    }) ;
    
    $.treeInit = function(jqueryObj,json4Options){
    	/*
    	if(json4Options.source == 'data'){
    		json4Options.dataProxy = {
    			type:'data',
    			value:window[json4Options.data]
    		};
    	}
    	*/
    	jqueryObj.lighttree(json4Options);
    }
    
    $.fn.tree = function(json_obj){
    	if( !this.length ){
    		alert("鍒濆鍖栨爲澶辫触銆傞€夋嫨鍣╗"+this.selector +"]涓嶅瓨鍦紝璇锋鏌ヤ功鍐欐槸鍚︽湁璇紒") ;
    		return ;
    	}
    	
    	if( $(this).data("treeWidget") ) return  $(this).data("treeWidget");
    	
		var oTreeWidget = new treeWidget();
		oTreeWidget.init($(this),json_obj);
		
		$(this).data("treeWidget",oTreeWidget) ;
		return oTreeWidget;
	};
	
	$.uiwidget.register("tree",function(selector){
		selector.each(function(){
			var options = $(this).attr( $.uiwidget.options )||"{}";
			eval(" var jsonOptions = "+options) ;
			$(this).tree(jsonOptions) ;
		});
	}) ;
	
	treeWidget = function(){
		this.$ = null;
		
		var events = ['getSelectedIds','getSelectNodes','getCurrentNode',
		              'refresh','expandAll','collapse','expandLevel','isLeaf','treeOption',
		              'deleteNode','updateNode','addNode','checkAll','checkNode','disableNode'] ;
		
		var me = this ;
		
		this.init = function(jquery_obj,json_obj){
			this.$ = jquery_obj;
			this.options = json_obj ;
			if(json_obj != undefined){
				 this.$.lighttree(json_obj);
			}
			
			$(events).each(function(){
				var event = this ;
				me[event] = function(){
					var args = [] ;
					for (var j = 0; j < arguments.length; j++) {
						args.push(arguments[j]);
					}
					return me.$[event].apply(me.$,args) ;
				}
			}) ;
		};
		
		this.reload = function(options){//bbit-tree-bwrap
			this.$.find(".bbit-tree-bwrap").remove();
			this.options = $.extend(this.options,options||{}) ;
			this.init(this.$,this.options) ;
		}
	};
})(jQuery);