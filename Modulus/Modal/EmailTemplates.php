<?php

namespace Framework\Modulus\Modal;

class EmailTemplates extends \Framework\Modulus\Query
{
  protected $_table = 'email-templates';

  public function get( $key )
  {
    $query = "SELECT `key`,`subject`,`message` FROM `{$this->_table}` WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
		return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }
}
