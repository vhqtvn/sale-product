<%@page pageEncoding="UTF-8"%>
<%@page import="bingo.security.SecurityContext"%>
<%@page import="bingo.common.core.ApplicationFactory"%>
<%@page import="bingo.dao.DaoFactory"%>
<%@page import="bingo.dao.IDao"%>
<%@page import="java.util.Map"%>
<%@page import="bingo.common.core.utils.StringUtils"%>
<%
	String account = request.getParameter("account") ;
	String isNew = request.getParameter("isNew") ;
	String localUserId = SecurityContext.getCurrentUser().getLoginId() ;
	IDao dao = DaoFactory.getDao() ;
	
	String remoteUserId = "" ;
	
	if(StringUtils.isNotEmpty(account)){
		//save
		if("true".equals(isNew)){
			dao.getJdbcDao().insert("insert into `user_convert` (`Local_UserId`, `Remote_UserId`, `Remote_Source`) values(?,?,'bingocc')",localUserId,account) ;
		}else{
			dao.getJdbcDao().update("update user_convert set Remote_UserId= ? where Local_UserId= ? and Remote_Source='bingocc'",account,localUserId) ;
		}
	}
		
	isNew = "false" ;
	Map<String,Object> map = dao.getJdbcDao()
			.queryForMap("select uc.remote_userid as REMOTE_USERID from user_convert uc where uc.local_userid = ? and uc.remote_source='bingocc'",localUserId) ;
	if( null == map ){
		isNew = "true" ;
	}else{
		remoteUserId = String.valueOf(map.get("REMOTE_USERID"))  ;
	}
%>
<style>
<!--
/* 全局样式 */
html,body,div,span,p,ul,li,a,b,strong,table,thead,tbody,th,tr,td,h1,h2,h3,h4,h5,h6,form,input,label,img,dl,dt,dd{margin:0; padding:0;}
html,body{overflow:hidden;height:100%;}
body{font-family:Tahoma; font-size:12px; color:#222222;width:100%; background-position:center center; background-repeat:repeat;}
a{text-decoration:none; outline:none; color:#0066FF; }
a:hover{ text-decoration:underline}
ul{list-style:none;}
.dis_no{display:none;}
img{ border:none;}
.clear{clear:both;}
img,input,button { vertical-align: middle;}

body {background-color:#FFF; position:relative;padding:30px 10px;}

label{
	font-weight:bold;
	display:block;
}
-->
</style>


<div>
	<form action="account_mapping.jsp" method="post">
		<input type="hidden" name="isNew" value="<%=isNew %>"/>
		<label>本地账号: </label>
		<label><%=localUserId %></label><br/>
		<label for="account">BingoCC帐号: </label><br/>
		<input type="text" name="account" value="<%=remoteUserId %>"/>
		<br/><br/><br/>
		<button type="submit">保存</button>
	</form>
</div>