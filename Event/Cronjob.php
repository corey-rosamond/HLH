<?php
/**
 * abstract Cronjob
 *
 * @package Framework\Event
 * @version 1.0.0
 */
namespace Framework\Event;

/**
 * This will be the event abstraction layer
 */
abstract class Cronjob extends \Framework\Support\Abstracts\Singleton
{

  /**
   * The messages array
   * @var     array       $messages
   * @access  protected
   */
  protected $messages = [];

  /**
   * The status that will be returned by the cron job
   * @var     array       $status
   * @access  protected
   */
  protected $status = true;

  /**
   * Add a message to the cronJobs message queue
   * @method  addMessage
   * @param   boolean     $type     Did it work or not
   * @param   string      $message  Some meaningfull message
   * @return  mixed                 Reference to new message in colleciton
   * @access  protected
   */
  protected function addMessage( $type, $message )
  { return array_push( $this->messages,['type'=>$type,'message'=>$message]); }

  /**
   * Return the end of job report with our completeion status and any
   * Messages we would like to logged
   * @method    returnReport
   * @return    array         An arry that contains the outcoe status and messsages we need logged
   * @access    protected
   */
  protected function returnReport($cCronTab, $data)
  { return ['status'=>(($this->status)?$cCronTab::statusIdle:$cCronTab->chooseFailureStatus($data)),'messages'=>$this->messages];}
}
