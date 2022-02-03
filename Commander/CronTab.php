<?php

namespace Framework\Commander;

class CronTab extends \Framework\Support\Abstracts\Singleton
{

  /** Modals */
  private $_mCronTab;
  private $_mCronLog;

  /** Tab Datatable Variables */
  private $_dTabPortletType  = 'box';
  private $_dTabPortletColor = 'blue-chambray';
  private $_dTabPortletIcon  = "fa fa-hourglass";
  private $_dTabPortletTitle = "CronJobs";
  private $_dTabDatatableID  = "CronTabDatatable";
  private $_dTabDatatableCol = [
    ["label"=>"Job #",      "style"=>"text-align:center;",  "order"=>true],
    ["label"=>"Job Name",   "style"=>"",                    "order"=>true],
    ["label"=>"Run Every",  "style"=>"text-align:center;",  "order"=>true],
    ["label"=>"Status",     "style"=>"text-align:center;",  "order"=>true],
    ["label"=>"Description","style"=>"",                    "order"=>true],
    ["label"=>"Run Order",  "style"=>"text-align:center;",  "order"=>true],
    ["label"=>"State",      "style"=>"text-align:center;",  "order"=>true],
    ["label"=>"Last Run",   "style"=>"",                    "order"=>true]
  ];

  /** Log Datatable Variables */
  private $_dLogPortletType  = 'box';
  private $_dLogPortletColor = 'blue-steel';
  private $_dLogPortletIcon  = "fa fa-hourglass";
  private $_dLogPortletTitle = "CronLog";
  private $_dLogDatatableID  = "CronLogDatatable";
  private $_dLogDatatableCol = [
    ["label"=>"Log #",          "style"=>"text-align:center;width:10%;","order"=>true],
    ["label"=>"Job Name",       "style"=>"width:15%;",                  "order"=>true],
    ["label"=>"Type",           "style"=>"text-align:center;width:5%;", "order"=>true],
    ["label"=>"Message",        "style"=>"width:45%;",                  "order"=>true],
    ["label"=>"Date &amp; Time","style"=>"text-align:center;width:25%;","order"=>true]
  ];

  /** STATUS CODES */
  const statusDisabled    = 0;
  const statusIdle        = 1;
  const statusRunning     = 2;
  const statusFailedLast  = 3;
  const statusFailure     = 4;
  private $_statusText    = [0=>"Disabled",1=>"Idle",2=>"Running",3=>"Failed Last!",4=>"Failure!"];
  private $_statusColor   = [0=>"danger",1=>"primary",2=>"success",3=>"warning",4=>"danger"];

  /** STATE CODES */
  const stateDisabled   = 0;
  const stateEnabled    = 1;
  private $_stateText   = [0=>"Disabled",1=>"Enabled"];
  private $_stateColor  = [0=>"danger",1=>"success"];

  /** TYPE CODES */
  const typeError       = 0;
  const typeNotice      = 1;
  private $_typeText    = [0=>"Error",1=>"Notice"];
  private $_typeColor   = [0=>"danger",1=>"success"];

  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_cDocument = \Framework\Core\Receptionist::controller( 'Document', true );
    $instance->_mCronTab  = \Framework\Core\Receptionist::modal( "CronTab", "Holylandhealth", true );
    $instance->_mCronLog  = \Framework\Core\Receptionist::modal( "CronLog", "Holylandhealth", true );
    return $instance;
  }

  public function getActiveJobs()
  { return $this->_mCronTab->getActiveJobs(); }

  public function updateLastRun( $job )
  { return $this->_mCronTab->updateLastRun( $job ); }

  public function setStatus( $job, $status )
  { return $this->_mCronTab->setStatus( $job, $status ); }

  public function log( $key, $type, $message )
  { return $this->_mCronLog->log($key, $type, $message); }

  public function chooseFailureStatus( $job )
  {
    if($job['status']!==self::statusFailedLast){
      return self::statusFailedLast;
    }
    self::statusFailure;
  }

  public function docketLastRun()
  {
    $date = date("l jS \of F Y h:i:s A",strtotime($this->_mCronTab->docketLastRun()));
    return "<span class=\"label label-default\">Last Run: {$date}</span>";
  }

  public function docketState()
  {
    $code = $this->_mCronTab->docketState();
    return "<span class=\"label label-{$this->_stateColor[$code]}\">State: {$this->_stateText[$code]}</span>";
  }

  public function docketStatus()
  {
    $code = $this->_mCronTab->docketStatus();
    return "<span class=\"label label-{$this->_statusColor[$code]}\">Status: {$this->_statusText[$code]}</span>";
  }

  public function cronTabDatatable()
  {
    return $this->_cDocument->portlet(
      $this->_dTabPortletType,
      $this->_dTabPortletColor,
      $this->_dTabPortletIcon,
      $this->_dTabPortletTitle,'',
      $this->_cDocument->datatable(
        $this->_dTabDatatableID,
        $this->_dTabDatatableCol,
        $this->_formatTaB( $this->_mCronTab->getCrontab())
      ),[],[],true
    );
  }

  public function cronLogDatatable()
  {
    return $this->_cDocument->portlet(
      $this->_dLogPortletType,
      $this->_dLogPortletColor,
      $this->_dLogPortletIcon,
      $this->_dLogPortletTitle,'',
      $this->_cDocument->datatable(
        $this->_dLogDatatableID,
        $this->_dLogDatatableCol,
        $this->_formatLog( $this->_mCronLog->getLog())
      ),[],[],true
    );
  }


  private function _formatTaB( $crontab )
  {
    foreach( $crontab as &$cronjob ){
      $cronjob['key']       = $cronjob['key'] = sprintf('#%03d', $cronjob['key']);
      $cronjob['interval']  = "{$cronjob['interval']} Minutes";
      $cronjob['status']    = $this->_status($cronjob['status']);
      $cronjob['active']    = $this->_state($cronjob['active']);
      $cronjob['last-run']  = (($cronjob['last-run']=="0000-00-00 00:00:00")?"Never":date("l jS \of F Y h:i:s A",strtotime($cronjob['last-run'])));
    }
    return $crontab;
  }

  private function _formatLog( $cronlog )
  {
    foreach( $cronlog as &$entry ){
      $entry['key']       = $entry['key'] = sprintf('#%08d',$entry['key']);
      $entry['message']   = (($entry['message']=="")?'No Message!':$entry['message']);
      $entry['type']      = $this->_type($entry['type']);
      $entry['timestamp'] = (($entry['timestamp']=="0000-00-00 00:00:00")?"Bad Entry":date("l jS \of F Y h:i:s A",strtotime($entry['timestamp'])));
    }
    return $cronlog;
  }

  private function _type( $code )
  { return "<span class=\"label label-{$this->_typeColor[$code]}\"> {$this->_typeText[$code]} </span>"; }

  private function _status( $code )
  { return "<span class=\"label label-{$this->_statusColor[$code]}\"> {$this->_statusText[$code]} </span>"; }

  private function _state( $code )
  { return "<span class=\"label label-{$this->_stateColor[$code]}\"> {$this->_stateText[$code]} </span>"; }

}
