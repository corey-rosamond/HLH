<?php
/**
 * EmailLinks
 *
 * @package App\Event\Page\Management\MediaBuy
 * @version 1.2.0
 */
namespace App\Event\Page\Management\MediaBuy;

/**
 * EmailLinks
 *
 * Tracking links
 */
\Framework\_IncludeCorrect( APP_ROOT . "Abstracts" . DSEP . "FunnelPage.php" );
class EmailLinks extends \App\Abstracts\Page
{
  /** @var boolean $requiresLogin */
  public static $requiresLogin = true;
  /** @var boolean|string $permissionGroup */
  public static $permissionGroup = false;
  /** @var boolean|string $permission */
  public static $permission = false;
  /** @var array $modal Modals you want the framework to pre-load */
  protected $modal = [];
  /** @var array $template Templates you want the framework to preload */
  protected $template = [];
  /** @var array $controller Controllers you want the framework to preload */
  protected $controller = [];
  /** @var array $pageHeader The header for this page */
  protected $pageHeader = "MediaBuy: ";
  /** @var string $pageSubHeader A small version of the hearder appears to the right of it */
  protected $pageSubHeader = "Links";

  /**
   * Advent calls this to build the page body
   * @method  body
   * @param   array   $paramaters   This is an optional array of paramtars that can be passed
   * @return  string                The html needed to render the body for this page
   * @access  public
   */
  public function Body(array $paramaters = array()){
    /** Set the document title */
    $this->setDocumentTitle('MediaBuy: Links');
    /** @var \Framework\Commander\Document $_cDocument */
    $this->_cDocument = \Framework\Core\Receptionist::controller('Document',true);
    /** Get the tracking Master datatable */
    $this->_datatableMediaBuyEmailLink = \Framework\Core\Receptionist::datatable('MediaBuyEmailLink');
    /** Write the datatable to the body */
    $this->writeBody($this->_cDocument->portlet('box','blue-sharp','fa fa-link','Tracking Links', '', $this->_datatableMediaBuyEmailLink->render(), [], []));
  }
}
