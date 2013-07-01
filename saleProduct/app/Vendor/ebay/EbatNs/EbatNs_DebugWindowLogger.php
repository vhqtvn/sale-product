<?php
	class EbatNs_DebugWindowLogger extends EbatNs_Logger
	{
		var $_out;
		function EbatNs_DebugWindowLogger(& $proxy)
		{
			$this->EbatNs_Logger(true, 'stdout');
			$proxy->attachLogger(& $this);
			$proxy->setLoggingOptions(array('LOG_TIMEPOINTS' => true, 'LOG_API_USAGE' => true));
		}
		
		function log($msg, $subject = null)
		{
			ob_start();
			parent::log($msg, $subject);
			$this->_out .= ob_get_clean();
		}
		
		function getDebugContent()
		{
			return '<div style="position:absolute;top:5px;left:600px;width:300px;height:400px;margin:10px auto;border:1px solid #000;border-right:none;text-align:left;padding:3px 5px;overflow:auto;" id="debugWindow">' 
			. $this->_out 
			. '</div>';
		}
	}
?>