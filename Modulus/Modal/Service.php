<?php

namespace Framework\Modulus\Modal;

class Service extends \Framework\Modulus\Query
{
  protected $_table = 'services';

  public function get( $key )
  {
    $query = "SELECT `key`,`name`,`type`,`configuration` FROM `{$this->_table}` WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
		return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }
}



/*
'key', 'int(11)', 'NO', 'PRI', NULL, ''
'name', 'varchar(150)', 'YES', '', NULL, ''
'type', 'int(11)', 'YES', '', NULL, ''
'configuration', 'text', 'YES', '', NULL, ''
*/
