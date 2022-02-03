<?php
/**
 * PDOdatabases
 *
 * @package Framework\Modulus
 * @version 1.0.0
 */
namespace Framework\Modulus;

/**
 * PDOdatabases
 *
 *This is a singleton instance of our database object
 *containing database connections
 *
 *First instantiation requires a config object passed to connect
 *After that it only requires the database name, as it stores the config object
 *Edit as you please, test with PDOdatabasesUT.php
 *
 *This will still need its exception class extended so that you can pass the object instance with the exception.
 */

class PDOdatabases
{
    //Instance of this objects
    /**
     * @var mixed
     */
    private static $instances = array();
    //Array of database connections
    /**
     * @var mixed
     */
    private $dbs = null;
    //Database config instance
    /**
     * @var mixed
     */
    private $_config = null;
    //Create an instance of this object
    public static function create_instance()
    {
      $cls = get_called_class();
      if (!isset(self::$instances[$cls])) {
          self::$instances[$cls] = new static;
      }
      return self::$instances[$cls];
    } //End create_instance()
    //This function connects to a database
    /**
     * @param $dbname
     * @param DBConfig $config
     * @return mixed
     */
    public function connect($dbname, DBConfig $config = null)
    {
      if(isset($this->dbs[$dbname])){
        return $this->dbs[$dbname];
      }

      if (!isset($this->_config[$dbname])) {
        if (null === $config) {
          throw new \Framework\Exceptional\PDOdatabasesException('Database configuration object is not set');
        }
        $this->_config[$dbname] = $config;
      }
      $config = $this->_config[$dbname];
      $this->dbs[$dbname] = new \PDO(
        $config::type .
        ':host=' . $config::$host .
        ';port=' . $config::$port .
        ';dbname=' . $config::$database,
        $config::$user,
        $config::$password
      );
      $this->dbs[$dbname]->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
      $this->dbs[$dbname]->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );
      return $this->dbs[$dbname];
    }
  public function resetconnection($dbname)
  {
    if ( !isset( $this->dbs[$dbname]) ){
      throw new \Framework\Exceptional\PDOdatabasesException('Database connection defined does not exist', 2 );
    }
    unset($this->dbs[ $dbname ]);
  }
}

//Exception class for the databases class
class PDOdatabasesException extends \Framework\Exceptional\BaseException
{}
