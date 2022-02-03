<?php
/**
 * CronLog
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * CronLog
 *
 * This model is used for the framework cron system. It help
 * keep track of previous cron job runs and their stats as well
 * as what cron jobs need to be run in the future
 */
class CronLog extends \Framework\Modulus\Query{

    public function getLog()
    {
        $query = "SELECT
            `log`.`key`, if(`log`.`cronjob-key`=0, 'Docket', `tab`.`simple-name`) as `simple-name`, `log`.`type`, `log`.`message`, `log`.`timestamp`
            FROM `system-cronlog` AS `log`
            LEFT JOIN `system-crontab` AS `tab`
            ON `log`.`cronjob-key` = `tab`.`key`
            ORDER BY `log`.`timestamp` DESC limit 0, 200";
        $preparedStatement = $this->Prepare( $query );
        $preparedStatement = $this->Execute( $preparedStatement );
        if( $preparedStatement->rowCount() <= 0 ){
      return false;
    }
        return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function log($key, $type, $message){
        $query = "INSERT INTO `system-cronlog` ( `cronjob-key`, `type`, `message` ) VALUES ( :key, :type, :message )";
        $preparedStatement = $this->Prepare($query);
        $preparedStatement->bindValue(':key', 		$key, 		\PDO::PARAM_INT);
        $preparedStatement->bindValue(':type', 		$type, 		\PDO::PARAM_INT);
        $preparedStatement->bindValue(':message', $message, \PDO::PARAM_STR);
        return $this->Execute($preparedStatement);
    }
}
