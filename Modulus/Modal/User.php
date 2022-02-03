<?php
/**
 * User
 *
 * @package Framework\Modulus\Modal
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * User
 *
 * This is your main interface to the user table
 */
class User extends \Framework\Modulus\Query
{
  /**
   * Set last active date
   * @method  setLastActive
   * @param   int             $key  User key
   * @return  boolean
   * @access  public
   */
  public function setLastActive( $key )
  {
    $query = "UPDATE `administration-users` SET `last-active`=NOW() WHERE `key`=:Key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':Key', $key, \PDO::PARAM_INT );
    return $this->Execute( $preparedStatement );
  }

  /**
   * Get the user data with the username
   * @method  userByName
   * @param   string                $username
   * @return  false|\PDOStatement
   * @access  public
   */
  public function userByName( $username )
  {
    $query = "SELECT * FROM `administration-users` WHERE `username`=:username";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':username', $username, \PDO::PARAM_STR );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }

  /**
   * Add a log entry for this users current action
   * @method  logAction
   * @param   int         $key      User key
   * @param   string      $message  The message to write to the log
   * @return  boolean               TRUE if no errors
   * @access  public
   */
  public function logAction( $key, $message )
  {
    $query = "INSERT INTO `administration-user-log` (`user-key`,`date-stamp`,`message`) VALUES (:UserKey,NOW(),:Message)";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':UserKey', $key, \PDO::PARAM_INT );
    $preparedStatement->bindValue( ':Message', $message, \PDO::PARAM_STR );
    return $this->Execute( $preparedStatement );
  }

  /**
   * Get the user list datatable
   * @method  getManageUserDatatable
   * @return  \PODResource
   * @access  public
   */
  public function getManageUserDatatable()
  {
    $query = 'SELECT `key`, `username`, CONCAT(`first-name`," ",`last-name`) as `name`, `email-address` as `email-address`, `phone-number`,
        DATE_FORMAT(`last-active`,"%b %d %Y %h:%i %p") as `last-login`, `active` as `status`, "" as `actions` FROM `administration-users` order by `key` desc';
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }

  /**
   * Get the recnet user log
   * @method  getRecentUserLog
   * @param   int              $key  User Key
   * @return  \PODResource
   * @access  public
   */
  public function getRecentUserLog( $key )
  {
    $query = "SELECT `key`, DATE_FORMAT(`date-stamp`,'%b %d %Y %h:%i %p') as `date-stamp`, `message` FROM `administration-user-log` WHERE `user-key`=:key ORDER BY `key` DESC LIMIT 0, 500";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key', $key, \PDO::PARAM_INT );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }

  /**
   * changeUserStatus
   * @method  changeUserStatus
   * @param   int               $key    User Key
   * @param   int               $status New User Status
   * @return  boolean                   TRUE if no erros
   * @access  public
   */
  public function changeUserStatus( $key, $status )
  {
    $query = "UPDATE `administration-users` SET `active`=:status WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key',     $key,     \PDO::PARAM_INT );
    $preparedStatement->bindValue( ':status',  $status,  \PDO::PARAM_INT );
    return $this->Execute( $preparedStatement );
  }


  /**
   * Get all of this users data via their user key
   * @method  getUserData
   * @param   integer $key User key
   * @return  \PDOResource      The User data
   * @access  public
   */
  public function getUserData( $key )
  {
    $query = "SELECT * FROM `administration-users` WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key', $key, \PDO::PARAM_INT );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }

  /**
   * Get the basic information for a user
   * @method  getUserInformation
   * @param   int                 $key  User key for the user in question
   * @return  \PODResource
   * @access  public
   */
  public function getUserInformation( $key )
  {
    $query = "SELECT `key`, `username`, `first-name`, `last-name`, `phone-number`, `email-address` FROM `administration-users` WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key', $key, \PDO::PARAM_INT );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }

  /**
   * update the user information and return the updated user information records
   * @method  updateUserInformation
   * @param   int                         $key          User key
   * @param   string                      $firstName    First name
   * @param   string                      $lastName     Last name
   * @param   string                      $phoneNumber  Phone number
   * @param   string                      $emailAddress Email address
   * @return  \PODResource                              Updated user information
   * @uses    getUserInformation()
   * @access  public
   */
  public function updateUserInformation( $key, $firstName, $lastName, $phoneNumber, $emailAddress )
  {
    $query = "UPDATE `administration-users` SET `first-name`=:firstName, `last-name`=:lastName, `phone-number`=:phoneNumber,
      `email-address`=:emailAddress WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key', $key, \PDO::PARAM_INT );
    $preparedStatement->bindValue( ':firstName', $firstName, \PDO::PARAM_STR );
    $preparedStatement->bindValue( ':lastName', $lastName, \PDO::PARAM_STR );
    $preparedStatement->bindValue( ':phoneNumber', $phoneNumber, \PDO::PARAM_STR );
    $preparedStatement->bindValue( ':emailAddress', $emailAddress, \PDO::PARAM_STR );
    $this->Execute( $preparedStatement );
    return $this->getUserInformation( $key );
  }

  /**
   * Update a single users permissions
   * @method  updateUserPermissions
   * @param   int                     $key         User key of the user we are working with
   * @param   string                  $permissions Serialized associative array of permissions
   * @return  string                               Serialized associative array of permissions
   * @uses    getUserPermissions()
   * @access  public
   */
  public function updateUserPermissions( $key , $permissions )
  {
    $query = "UPDATE `administration-users` SET `user-permissions`=:permissions WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key', $key, \PDO::PARAM_INT );
    $preparedStatement->bindValue( ':permissions', $permissions, \PDO::PARAM_STR );
    $this->Execute( $preparedStatement );
    return $this->getUserPermissions( $key );
  }

  /**
   * get the the users permissions object
   * @method  getUserPermissions
   * @param   int                 $key    User key of the user we are working with
   * @return  string                      Serialized associative array of permissions
   * @access  public
   */
  public function getUserPermissions( $key )
  {
    $query = "SELECT `user-permissions` FROM `administration-users` WHERE `key`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':key', $key, \PDO::PARAM_INT );
    $preparedStatement = $this->Execute( $preparedStatement );
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource[ 'user-permissions' ];
  }

  /**
   * Search for a user with a term
   * @method  userSearch
   * @param   string                $term   The term we are searching for
   * @return  \PODResource|false            FALSE if no results
   * @access  public
   */
  public function userSearch( $term )
  {
    $query = "SELECT `key` as `id`, CONCAT( `first-name`,' ',`last-name`) as `text` FROM `administration-users`
      WHERE CONCAT(`first-name`,' ',`last-name`) LIKE :term";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':term', "%$term%", \PDO::PARAM_STR );
    $preparedStatement = $this->Execute( $preparedStatement );
    if( $preparedStatement->rowCount() <= 0 ){ return false; }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }
}
