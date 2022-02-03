<?php
/**
 * Cron
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * Cron
 *
 * This model is used for the framework cron system. It help
 * keep track of previous cron job runs and their stats as well
 * as what cron jobs need to be run in the future
 */
class Cron extends \Framework\Modulus\Query
{
	/**
	 * Get a list of the active cron jobs
	 * @return false|\PDOResource
	 * @access public
	 */
	public function getJobs()
  {
		$query = "SELECT * FROM `system-cronjobs` WHERE `active`=1 ORDER BY `order`";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){ 
      return false;
    }
		return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * gets the minimum data to run a job
	 * @return false|\PDOResource
	 * @access public
	 */
	public function getJobsMin()
  {
		$query = "SELECT `key`, `simple-name`, `class-name`, `is-framework`, `interval`, `last-run` FROM `system-cronjobs` WHERE `active`=1 ORDER BY `order`";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
		return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
	}

	/**
	 * Updates
	 * @return [type] [description]
	 */
	public function updateJobStatus( $key, $status )
  {
		$query = "UPDATE `system-cronjobs` SET `status`=:status, `last-run`= NOW() WHERE `key` = :key";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement->bindValue(':key',     $key,     \PDO::PARAM_INT);
		$preparedStatement->bindValue(':status',  $status,  \PDO::PARAM_INT);
		return $this->Execute( $preparedStatement );
	}

}
