<?php
/**
 * Query
 *
 * @package Framework\Modulus
 * @version 1.0.0
 */
namespace Framework\Modulus;

/**
 * Query
 *
 * The main query object. Every model must extend this
 */
class Query
{
    //Database name
    /**
     * @var mixed
     */
    private $DBName;

    //PDODatabases instance
    /**
     * @var mixed
     */
    private $PDODatabases;

    //Database handle
    /**
     * @var mixed
     */
    private $DB;

    /**
     * @param $dbname
     * @param DBConfig $config
     */
    public function __construct($dbname, DBConfig $config)
    {
        //Created the PDOdatabases instance
        $this->PDODatabases = PDOdatabases::create_instance();

        //set the database name for reconnection
        $this->DBName = $dbname;

        //Connect!
        $this->Connect($dbname, $config);
    }

    //Initial connection to database
    /**
     * @param $dbname
     * @param DBConfig $config
     */
    private function Connect($dbname, DBConfig $config)
    {
        //Connect to the database
        $this->DB = $this->PDODatabases->connect($dbname, $config);
    }

    //This is called if the database connection goes stale
    private function Reconnect()
    {
        $this->PDODatabases->resetconnection($this->DBName);
        $this->DB = $this->PDODatabases->connect($this->DBName);
    }

    //This prepares a statement
    /**
     * @param $query
     * @return mixed
     */
    protected function Prepare($query)
    {
        try {
            return $this->DB->prepare($query);
        } catch (\PDOException $e) {
            throw new \Framework\Exceptional\PDOdatabasesException(
                $e->getMessage(), 42, 1, $e->getFile(), $e->getLine(), $e);

        }

    }

    
    protected function StartTransaction()
    {
      $this->DB->beginTransaction();
    }

    
    protected function RollBackTransaction()
    {
      $this->DB->rollBack();
    }

    
    protected function CommitTransaction()
    {
      $this->DB->commit();
    }

    /**
     * @param PDOStatement $PreparedStatement
     * @return mixed
     */
    protected function Execute(\PDOStatement $PreparedStatement)
    {
      try {
        $PreparedStatement->execute();
      } catch (\PDOException $e) {
        if( $e->getCode() != 2006 ){ 
          throw $e;
        }
        $this->Reconnect();
        $this->Execute( 
          $PreparedStatement 
        );
      }
      return $PreparedStatement;
    }
    //Get the last inserted id
    /**
     * @return mixed
     */
    protected function LastInsertID()
    {
        return $this->DB->lastInsertId();
    }
}
