<?php
/**
 * Docket
 *
 * @package Framework\Core
 * @version 1.2.0
 */
namespace Framework\Core;

/**
 * Docket
 *
 * Docket is the cronjob interface it makes cron jobs simple. You have
 * one table of cron jobs that need to run. This goes through runs them in order
 * and takes the appropriate action dependent on the outcome
 * /var/www/html/framework/Modulus/Modal/Order.php
 * php-cgi -f cron.php system=administration.ones-n-zeros.com
 */
class Docket extends Core
{
  /** @var Admit $_coreAdmit */
  private $_coreAdmit;
  /** @var Contour $_coreContour */
  private $_coreContour;
  /** @var Receptionist $_coreReceptionist */
  private $_coreReceptionist;
  /** @var \Framework\Commander\CronTab $_cCrontab */
	private $_cCronTab;
  /** @var string $_dNamespace */
	private $_dNamespace = "\\Framework\\Docket\\";
  /** @var array $_dCronDirectory */
  private $_dCronDirectory;

  public function construct()
  {
    /** @var Architect $instance */
    $instance = self::getInstance();
    try{
      /** @var Contour _coreContour */
      $instance->_coreContour = Contour::getInstance();
      /** @var Contour _coreContour */
      $instance->_coreContour = Contour::getInstance();
      /** @var Receptionist _coreReceptionist */
      $instance->_coreReceptionist = Receptionist::getInstance();
      /** @var Admit _coreAdmit */
      $instance->_coreAdmit = Admit::getInstance();
      /** @var \Framework\Commander\CronTab $_cCronTab */
      $instance->_cCronTab = Receptionist::controller('CronTab', true);
      /** Set the CRON Directory for Framework  */
      $instance->_dCronDirectory = [
        self::rTypeFramework => FRAMEWORK_ROOT."Docket".DSEP,
        self::rTypeApplication => APP_CRONTAB
      ];
		} catch ( \Framework\Exceptional\BaseException $exception ){
			throw new \Framework\Exceptional\DocketFault(
				$exception->getMessage(),
				$exception->getCode(),
				$exception->getSeverity(),
				$exception->getFile(),
				$exception->getLine(),
				$exception
			);
		}
	}

  public function run()
  {
    /** Update the docket last run time */
    $this->_cCronTab->updateLastRun(0);
    /** Set dockets status as running */
    $this->_cCronTab->setStatus(0,\Framework\Commander\CronTab::statusRunning);
    /** Get a list of active cron jobs */
    $jobs = $this->_cCronTab->getActiveJobs();
    /** Check if we have jobs */
    if(!$jobs){
      /** Log nothing to do */
      $this->_cCronTab->log(0, \Framework\Commander\CronTab::typeNotice, "No CronJobs to run Exiting!");
      /** Set dockets status back to idle */
      $this->_cCronTab->setStatus(0,\Framework\Commander\CronTab::statusIdle);
      return true;
    }
    /** Loop through the jobs */
    foreach($jobs as $job){
      /** Do the job */
      $this->doJob( $job, $this->_cCronTab );
    }
    $this->_cCronTab->setStatus(0,\Framework\Commander\CronTab::statusIdle);
    exit();
  }

  /**
   * Do a selected cron job
   * @method   doJob
   * @param    array   $job  The job array
   * @return   array
   */
  private function doJob($job)
  {
    /** Check if the job is already done */
    if($this->jobDone($job['last-run'], $job['interval'])){
      return true;
    }
    try{
      $this->includeJob($job['class-name'], $job['is-framework']);
    } catch (\Framework\Exceptional\IncludeOnceException $exception){
      $this->_cCronTab->setStatus($job['key'], $this->_cCronTab->chooseFailureStatus($job));
      return $this->_cCronTab->log($job['key'], \Framework\Commander\CronTab::typeError, "Unable to include cronJob!");
    }
    $this->_cCronTab->updateLastRun($job['key']);
    $this->_cCronTab->setStatus($job['key'],\Framework\Commander\CronTab::statusRunning);
    $activeJob = $this->_dNamespace . $job['class-name'];
    $return = $activeJob::run($job, $this->_cCronTab);
    if( count($return['messages']) > 0 ){
      foreach( $return['messages'] as $message ){
        $this->_cCronTab->log($job['key'], $message['type'], $message['message']);
      }
    }
    $this->_cCronTab->setStatus($job['key'],$return['status']);
    return true;
  }

  /**
   * Checks to see if cron is done.
   * @method jobDone
   * @param integer $last
   * @param integer $interval
   * @return boolean
   */
  private function jobDone($last, $interval)
  {
    if((strtotime($last)+(60*$interval))<time()){
      return false;
    }
    return true;
  }

  /**
   * Gives a new cron job to run.
   * @method   includeJob
   * @param    string      $class
   * @param    boolean     $isFramework
   * @return   boolean
   */
  private function includeJob($class, $isFramework)
  {
    $system = (($isFramework==='1')?self::rTypeFramework:self::rTypeApplication);
    \Framework\_IncludeCorrect("{$this->_dCronDirectory[$system]}{$class}.php");
    return true;
  }
}
