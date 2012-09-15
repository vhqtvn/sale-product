<%@ page language="java" import="java.util.*" pageEncoding="UTF-8"%>
<%@page import="ez.ui.core.CssController"%>
<%@page import="ez.ui.config.ConfigContext"%>
<%@page import="org.apache.commons.lang.StringUtils"%>

<%
	String themes = request.getParameter("themes") ;
	//System.out.println(themes) ;
	CssController css = new CssController() ;
	
	String cssFile = request.getParameter("cssFile") ;
 	 if(StringUtils.isEmpty(cssFile)){
 		cssFile = "Css.Template.Path" ;
 	 }
	
	String fileString = ConfigContext.getConfig(cssFile).getFileString() ;
	String source = css.parse(fileString,themes) ;
	themes = null ;
	//System.out.println(source) ;

	response.reset();
	
   	response.setContentType("application/x-download");
   	response.addHeader("Content-Disposition", "attachment;filename=my.css");
   	response.setCharacterEncoding("GBK") ;
   	
   	java.io.OutputStream outp = null;
   	java.io.FileInputStream in = null;
   	try {
   		outp = response.getOutputStream() ;
   		
   		outp.write(source.getBytes()) ;
   		
   		outp.flush();
   		out.clear();
   		out = pageContext.pushBody();
   	} catch (Exception e) {
   		System.out.println("Error!");
   		e.printStackTrace();
   	} finally {
   		if (in != null) {
   			in.close();
   			in = null;
   		} 
   		source = null ;
   	}
   	%>
