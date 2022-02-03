<?php
/**
 * Tracking
 *
 * @package Framework\Modulus\Model
 * @version 1.0.0
 */
namespace Framework\Modulus\Modal;

/**
 * Tracking
 *
 * This is the main interface for tracking records
 */
class Tracking extends \Framework\Modulus\Query
{
  /** SESSIONS */
  private $_sTable  = "funnel-sessions";
  private $_stKey   = "key";
  /** VIEWS */
  private $_vTable  = "funnel-session-views";
  private $_vtKey   = "key";
  private $_vptKey  = "funnel-page-key";
  private $_vstKey  = "funnel-session-key";
  /** PAGES */
  private $_pTable  = "funnel-pages";
  private $_ptKey   = "key";
  /** EMAIL LINKS */
  private $_elTable  = "mediabuy-email-links";
  private $_eltKey   = "key";

  public function session( $key )
  {
    $query = "SELECT `key`, INET6_NTOA(`ip-address`) as `ip-address`, `campaign-key`, `time-stamp`, `funnel-key`
      FROM `{$this->_sTable}` WHERE `{$this->_stKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }

  public function link( $key )
  {
    $query =
    "SELECT * FROM `{$this->_elTable}` WHERE `{$this->_eltKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    return $preparedStatement->fetch( \PDO::FETCH_ASSOC );
  }

  /**
   * Page views for a specific session key
   * @method  viewsForSession
   * @param   int     $key    The session-key for the order we want to access
   * @return  array           Array of page views of the submited session-key
   * @access  public
   */
  public function viewsForSession( $key )
  {
    $query =
    "SELECT
      `{$this->_vTable}`.`time-stamp` as `timestamp`,
      `{$this->_pTable}`.`name` as `page-name`,
      'placeholder' as `time-on-page`
    FROM `{$this->_vTable}`
    LEFT JOIN `{$this->_pTable}`
      ON `{$this->_vTable}`.`{$this->_vptKey}` = `{$this->_pTable}`.`{$this->_ptKey}`
      WHERE `{$this->_vstKey}` = :key
      ORDER BY `{$this->_vTable}`.`time-stamp` desc";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return [];
    }
    return $preparedStatement->fetchAll( \PDO::FETCH_ASSOC );
  }

  /**
   * Count of total page views for a specific session
   * @method  viewCountForSession
   * @param   int     $key  The session-key for the order we want to access
   * @return  int           integer representation of total page vies for the submitted session-key
   * @access  public
   */
  public function viewCountForSession( $key )
  {
    $query = "SELECT count(`{$this->_vtKey}`) AS `total` FROM `{$this->_vTable}` WHERE `{$this->_vstKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['total'];
  }

  /**
   * Get the uncompacted version of the ip address for the submitted session key
   * @method  getVisitorIpAddress
   * @param   int     $key  The session-key for the order we want to access
   * @return  string        Ip address string for this session-key
   * @access  public
   */
  public function getVisitorIpAddress( $key )
  {
    $query = "SELECT INET6_NTOA(`ip-address`) as `ip-address` FROM `{$this->_sTable}` WHERE `{$this->_stKey}`=:key";
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute( $preparedStatement );
    if($preparedStatement->rowCount() == 0){
      return 0;
    }
    $resource = $preparedStatement->fetch( \PDO::FETCH_ASSOC );
    return $resource['ip-address'];
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                        CONVERT TO NEW FORMAT                                        */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**
   * Make a session in the funnel-sessions table
   * @method  makeSession
   * @param   string      $ipAddress
   * @param   int         $campaignKey
   * @return  boolean
   */
  public function makeSession( $ipAddress, $campaignKey )
  {
    $query = 'INSERT INTO `funnel-sessions` ( `ip-address`, `campaign-key` ) VALUES ( :ipAddress, :campaignKey )';
    $preparedStatement = $this->Prepare( $query );
    $preparedStatement->bindValue( ':ipAddress', $ipAddress, \PDO::PARAM_INT );
    $preparedStatement->bindValue( ':campaignKey', $campaignKey, \PDO::PARAM_INT );
    return $this->Execute( $preparedStatement );
  }

  /**
   * @method getLinkImpressions
   * @param  int             $key linkKey
   * @return int                  total impressions
   */
  public function getLinkImpressions( $key )
  {
    $query = 'SELECT count(`key`) as `total` FROM `funnel-sessions` WHERE `campaign-key`=:key';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return 0;}
    $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
    return $resource['total'];
  }


  public function getPageImpressions($key)
  {
    $query = '
      select
        `funnel-page-key`,
        count(`fsv`.`key`) as `impressions`
      FROM `funnel-sessions` as `fs`
      LEFT JOIN `funnel-session-views` as `fsv`
      ON `fs`.`key`=`fsv`.`funnel-session-key`
      WHERE `fs`.`campaign-key` = :key
      GROUP BY `fsv`.`funnel-page-key`';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement->bindValue(':key', $key, \PDO::PARAM_INT);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return 0;}
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getPageTypes()
  {
    $query = 'SELECT `key`,`name` FROM `funnel-pages`';
    $preparedStatement = $this->Prepare($query);
    $preparedStatement = $this->Execute($preparedStatement);
    if ($preparedStatement->rowCount() == 0) {return 0;}
    return $preparedStatement->fetchAll(\PDO::FETCH_ASSOC);
  }


  public function getImpressionsByPageAndCampaign( $page, $link )
  {
    $query = '
        select DISTINCT count( `fsv`.`key` ) as `impressions`
        FROM `funnel-sessions` as `fs`
        LEFT JOIN `funnel-session-views` as `fsv`
        ON `fs`.`key`=`fsv`.`funnel-session-key`
        WHERE `fs`.`campaign-key` = :link
        AND `fsv`.`funnel-page-key`=:page';
      $preparedStatement = $this->Prepare($query);
      $preparedStatement->bindValue(':page', $page, \PDO::PARAM_INT);
      $preparedStatement->bindValue(':link', $link, \PDO::PARAM_INT);
      $preparedStatement = $this->Execute($preparedStatement);
      if ($preparedStatement->rowCount() == 0) {return 0;}
      $resource = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
      return $resource['impressions'];
  }
}
