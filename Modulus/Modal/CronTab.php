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
class CronTab extends \Framework\Modulus\Query
{

	public function getCrontab()
	{
		$query = "SELECT `key`, `simple-name`, `interval`, `status`, `simple-description`, `order`, `active`, `last-run` FROM `system-crontab` WHERE `key`!=0 ORDER BY `order`";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
		return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function docketLastRun()
	{
		$query = "SELECT `last-run` FROM `system-crontab` WHERE `key`=0";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		$resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
		return $resource['last-run'];
	}

	public function docketState()
	{
		$query = "SELECT `active` FROM `system-crontab` WHERE `key`=0";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		$resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
		return $resource['active'];
	}

	public function docketStatus()
	{
		$query = "SELECT `status` FROM `system-crontab` WHERE `key`=0";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		$resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
		return $resource['status'];
	}

	public function getActiveJobs()
	{
		$query = "SELECT * FROM `system-crontab` WHERE `key`!=0 AND `active`=1 ORDER BY `order`";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement = $this->Execute( $preparedStatement );
		if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
		return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function updateLastRun( $key )
  {
		$query = "UPDATE `system-crontab` SET `last-run` = NOW() WHERE `key`=:key";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
		return $this->Execute( $preparedStatement );
	}

  public function setStatus( $key, $status )
  {
		$query = "UPDATE `system-crontab` SET `status` = :status WHERE `key`=:key";
		$preparedStatement = $this->Prepare( $query );
		$preparedStatement->bindValue(':status', 	$status, \PDO::PARAM_INT);
		$preparedStatement->bindValue(':key', 		$key, 	\PDO::PARAM_INT);
		return $this->Execute( $preparedStatement );
	}

}
