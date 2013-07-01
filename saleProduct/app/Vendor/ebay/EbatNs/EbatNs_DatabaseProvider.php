<?php
// $Id: EbatNs_DatabaseProvider.php,v 1.1 2007/05/31 11:38:00 michael Exp $
/* $Log: EbatNs_DatabaseProvider.php,v $
/* Revision 1.1  2007/05/31 11:38:00  michael
/* - initial checkin
/* - version < 513
/*
 * 
 * 4     29.05.06 9:59 Charnisch
 * 
 * 3     3.02.06 10:44 Mcoslar
 * 
 * 2     30.01.06 16:44 Mcoslar
 * �nderungen eingef�gt
*/
	//
	// this class abstracts database-access
	// primaray builded for mysql-access
	//
	class EbatNs_DatabaseConfig
	{
		var $_host;
		var $_database;
		var $_user;
		var $_password;
		
		function setConfig($host, $db, $user, $password)
		{
			$this->_host = $host;
			$this->_database = $db;
			$this->_user = $user;
			$this->_password = $password;
		}
	}

	class EbatNs_DatabaseProvider extends EbatNs_DatabaseConfig
	{
		var $_dbHandle;
		function EbatNs_DatabaseProvider()
		{
		}
		
		function getConnection()
		{
			if ( $this->_dbHandle === null )
			{
				$this->_dbHandle = mysql_pconnect( $this->_host, $this->_user, $this->_password, 0 ) or die( mysql_error() );
				mysql_select_db( $this->_database, $this->_dbHandle );
			} 
			return $this->_dbHandle;
		}
		
		function getGeneratedId()
		{
			return mysql_insert_id( $this->getConnection() );
		} 
		
		function executeInsert($table, $rowData)
		{
			$sql = 'insert into ' . $table . ' ';
			$sql .= '(' . join( ',', array_keys( $rowData ) ) . ') ';
			$sql .= 'values (';

			foreach ( $rowData as $k => $v )
			{
				$rowData[$k] = "'" . addslashes( $v ) . "'";
			} 

			$sql .= join( ',', $rowData ) . ')';
			$this->executeSql( $sql );
			return $this->getGeneratedId();
		}
		
		function executeUpdate($table, $rowData, $priKeyName, $priKeyValue, $extraCondition = null)
		{
			$sql = 'update ' . $table . ' set ';

			if ( array_key_exists( $priKeyName, $rowData ) )
				unset( $rowData[$priKeyName] );

			$updateData = array();
			foreach ( $rowData as $k => $v )
			{
				$updateData[] = $k . "= '" . addslashes( $v ) . "'";
			} 

			$sql .= join( ',', $updateData );
			$sql .= " where $priKeyName ='$priKeyValue'";
			if ( $extraCondition )
				$sql .= ' and ' . $extraCondition;
			return $this->executeSqlNoQuery( $sql, null);
		}

		function executeDelete( $table, $priKeyName, $priKeyValue)
		{
			$sql = 'delete from ' . $table;
			$sql .= ' where ' . $priKeyName . "='" . $priKeyValue . "'";
			return $this->executeSqlNoQuery( $sql );
		} 

		function executeSqlNoQuery( $sql, $dbHandle = null)
		{
			$rs = $this->executeSql( $sql, $db);
			if ( $rs )
			{
				if ( $dbHandle === null )
					return mysql_affected_rows( $this->_dbHandle );
				else
					return mysql_affected_rows( $dbHandle );
			} 
			else
			{
				return 0;
			}
		} 
		// executes a sql-statement against the db. Any errors will be printed on screen !
		function executeSql ( $sql, $dbHandle = null)
		{
			if ( $dbHandle == null )
				$dbHandle = $this->getConnection();

			$rs = mysql_query( $sql, $dbHandle );

			return $rs;
		} 
		
		// Execute the statement and then fetches ALL rows
		function querySqlSet( $sql, $dbHandle = null)
		{
			if ( $dbHandle === null )
			{
				$dbHandle = $this->getConnection();
			} 

			$rs = $this->executeSql( $sql, $dbHandle );
			$rows = array();
			while ( $row = @mysql_fetch_assoc( $rs ) )
			{
				if ( $row )
					$rows[] = $row;
			} 

			@mysql_free_result( $rs );

			return $rows;
		}
		
		function querySql( $sql, $dbHandle = null)
		{
			if ( $dbHandle === null )
			{
				$dbHandle = $this->getConnection();
			} 

			$rs = $this->executeSql( $sql, $dbHandle );
			
			$row = @mysql_fetch_assoc( $rs );
			@mysql_free_result( $rs );

			return $row;
		}  		
	}
?>