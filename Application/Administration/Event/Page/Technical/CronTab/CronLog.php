<?php
/**
 * CronLog
 *
 * @package App\Event\Page\Main
 * @version 1.2.0
 */
namespace App\Event\Page\Technical\CronTab;

/**
 * CronLog
 *
 * This is the cronlog
 */
class CronLog extends \App\Abstracts\Page
{
  /****************************************************/
  /*                  PAGE SETTINGS                   */
  /****************************************************/
  /** @var string $documentTitle */
  protected $documentTitle = "Funnel: Click Through Rate";
  /** @var array $pageHeader The header for this page */
  protected $pageHeader = "Click Through Rate: ";
  /** @var array $scripts Array of script files we need framework to include */
  protected $_pageScripts = [];
  /****************************************************/
  /*               REQUIRED CONTROLLERS               */
  /****************************************************/

  /**
   * This will build the body of the document out the rest of the sections of the
   * page will be handled by the Application\Page abstract or by the
   * Advent\Page Abstract
   * @method  body
   * @param   array $parameters This is an optional array of paramtars that can be passed
   * @return  string                The html needed to render the body for this page
   * @access  public
   */
  public function Body(array $parameters = array() )
  {
    $CronTab  = $this->receptionist(self::oTypeController, 'CronTab');
    $this->_cDocument->addTab("CronTab", $CronTab->cronTabDatatable(), false, false, true);
    $this->_cDocument->addTab("CronLog", $CronTab->cronLogDatatable(), false, false, false);
    return $this->_cDocument->portlet(
        "light portlet-fit portlet-datatable bordered font-dark",
        '',
        'fa fa-hourglass',
        "Docket <span style=\"font-weight:normal;margin-left:10px;\">{$CronTab->docketState()}{$CronTab->docketStatus()}{$CronTab->docketLastRun()}</span>",
        '',
        $this->_cDocument->renderTabSystem(),
        [],
        []
      );
  }
}
