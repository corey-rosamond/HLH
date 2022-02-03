<?php
/**
 * ClickThroughRate
 *
 * @package App\Event\Reports\LinkSystem
 * @version 1.2.0
 */
namespace App\Event\Page\Reports\Funnel;

/**
 * ClickThroughRate
 *
 * Click through rate report
 */
class ClickThroughRate extends \App\Abstracts\Page
{
  /****************************************************/
  /*                  PAGE SETTINGS                   */
  /****************************************************/
  /** @var string $documentTitle */
  protected $documentTitle = "Funnel: Click Through Rate";
  /** @var array $pageHeader The header for this page */
  protected $pageHeader = "Click Through Rate: ";
  /** @var array $scripts Array of script files we need framework to include */
  protected $_pageScripts = [["Reports.Funnel.ClickThroughRate.js", self::application]];
  /****************************************************/
  /*               REQUIRED CONTROLLERS               */
  /****************************************************/
  /** @var \Framework\Commander\Funnel $_cFunnel */
  private $_cFunnel;

  /**
   * Build the body of the document
   * @method Body
   * @param array $parameters
   * @return string
   */
  public function Body(array $parameters = array()){
    $datatable = $this->receptionist(self::oTypeDatatable,"ClickThroughRate");
    /** Set Our portlet title */
    $this->_dPortletConfiguration['title'] = "Click Through Rate";
    /** Set the portlet tools */
    $this->_dPortletConfiguration['tools'] = ['config'=>['data-onclick'=>'popup:Reports.Funnel.ClickThroughRate.Configuration']];
    /** Create the datatable */
    $this->_dPortletConfiguration['content'] = $datatable->render();
    /** @var \Framework\Commander\Funnel $_cFunnel */
    $this->_cFunnel = $this->receptionist(self::oTypeController,'Funnel');
    /** Return the page content */
    return $this->portlet().$this->_configuration();
  }

  /**
   * Configuration popup
   * @method _configuration
   * @return string
   */
  private function _configuration()
  {
    return '
    <div id="Reports.Funnel.ClickThroughRate.Configuration" class="modal fade">
      <div class="modal-header bg-grey-steel">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <span class="fa-stack fa-lg">
            <i class="fa fa-square-o fa-stack-2x font-blue-ebonyclay"></i>
            <i class="fa fa-times fa-stack-1x font-blue-ebonyclay" style="margin-top:-2px;margin-left:0px;"></i>
          </span>
        </button>
        <h4 class="modal-title font-blue-ebonyclay"> <i class="fa fa-link"></i>&nbsp;&nbsp;Options </h4>
      </div>
      <div class="modal-body">
        <form class="form-inline editableform" id="select-link-form">
          <table class="table table-bordered table-striped">
            <tbody>
              <tr>
                <td style="width:30%"> Select a Funnel: </td>
                <td style="width:70%">'.$this->_cFunnel->buildFunnelSelect(null).'</a></td>
              </tr>
              <tr>
                <td style="width:30%"> Select a Link: </td>
                <td style="width:70%"><select id="emailLink"></select></td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
      <div class="modal-footer bg-grey-steel">
        <button type="button" data-dismiss="modal" class="btn btn-default btn-outline dark border-blue-dark">
          <i class="fa fa-times fa-lg" style="margin-top:-1px;"></i> Close
        </button>
        <button type="button" class="btn btn-default green bold border-blue-dark save-model" id="save-options">
          <i class="fa fa-check fa-lg" style="margin-top:-1px;"></i> Save changes
        </button>
      </div>
    </div>';
  }
}
