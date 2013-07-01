<?php 
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Carsten Harnisch <phpat@intradesys.com>                      |
// +----------------------------------------------------------------------+

// $Id: EbatNs_QueryResultInfo.php,v 1.1 2007/05/31 11:38:00 michael Exp $:
require_once 'EbatNs_Defines.php';
require_once 'EbatNs_Result.php';
require_once 'EbatNs_Session.php';
/**
 * Base class to ResultInformation returned by getResultInfo in all Query objects.
 * 
 * @see EbatNs_FeedbackQueryInfo
 * @see EbatNs_ProductQueryInfo
 * @see EbatNs_AccountQueryInfo
 * @see EbatNs_CrossPromotionQueryInfo
 * @see EbatNs_DescriptionTemplateQueryInfo
 * @see EbatNs_ItemCategoryQueryInfo
 * @see EbatNs_CategoryQueryInfo
 * @see EbatNs_DisputeQueryInfo
 * @see EbatNs_BidQueryInfo
 * @see EbatNs_CharacteristicSetQueryInfo
 * @see EbatNs_ItemQueryInfo
 * @see EbatNs_CategoryCSQueryInfo
 * @see EbatNs_ProductFamilyQueryInfo
 * @see EbatNs_ItemShippingQueryInfo
 * @see EbatNs_TransactionByItemQueryInfo
 * @see EbatNs_EventQueryInfo
 * @see EbatNs_TransactionQueryInfo
 * @see EbatNs_ItemBidderQueryInfo
 * @see EbatNs_ItemSellerQueryInfo
 * @see EbatNs_ItemWatchlistQueryInfo
 */
class EbatNs_QueryResultInfo {
  // this array holds all attribute data of the object
  var $_props = array();
  /**
   * sets a property by name and value
   */
  function _setProp($key, $value)
  {
    $this->_props[$key] = $value;
  }
  /**
   * gets a property by name
   */
  function _getProp($key)
  {
    return $this->_props[$key];
  }
  /**
   * Read accessor of ResultCount.
   * specifies the total number of elements with a resultset. Take care that if pagination support was used to retrieve the data, this will not always match the number of element for the current call.
   * 
   * @access public 
   * @return number Value of the ResultCount property
   */
  function getResultCount()
  {
    return $this->_props['ResultCount'];
  }
  /**
   * Write accessor of ResultCount.
   * specifies the total number of elements with a resultset. Take care that if pagination support was used to retrieve the data, this will not always match the number of element for the current call.
   * 
   * @access public 
   * @param number $value The new value for the ResultCount property
   * @return void 
   */
  function setResultCount($value)
  {
    $this->_props['ResultCount'] = $value;
  }
  /**
   * Standard init function, should be called from the constructor(s)
   */
  function _init()
  {
    $this->_props['ResultCount'] = null;
  }
}
?>