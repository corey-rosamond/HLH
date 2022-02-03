<?php

namespace Framework\Commander;

class OrderNotes extends \Framework\Support\Abstracts\Singleton
{

  private $_cDocument;

  /** Modals */
  private $_mOrderMessages;

  /** Tab Datatable Variables */
  private $_dTabPortletType  = 'box';
  private $_dTabPortletColor = 'blue-chambray';
  private $_dTabPortletIcon  = "fa fa-hourglass";
  private $_dTabPortletTitle = "Order Messages";
  private $_dTabDatatableID  = "MessagesDatatable";
  private $_dTabDatatableCol = [
    ["label"=>"Message #",        "style"=>"text-align:center;width:7%",    "order"=>true],
    ["label"=>"Order #",          "style"=>"text-align:center;width:7%",    "order"=>true],
    ["label"=>"User",             "style"=>"width:15%",                     "order"=>true],
    ["label"=>"Message",          "style"=>"width:41%",                     "order"=>true],
    ["label"=>"Date &amp; Time",  "style"=>"width:20%",                     "order"=>true]
  ];




  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_cDocument   = \Framework\Core\Receptionist::controller( 'Document', true );
    $instance->_mOrderNotes = \Framework\Core\Receptionist::modal( "OrderNotes", true );
    return $instance;
  }

  public function orderNoteCount($orderKey)
  { return $this->_mOrderNotes->countOrderNote($orderKey); }

  public function addOrderNote( $order, $user, $message )
  { return $this->_mOrderNotes->addOrderNote( $order, $user, $message ); }

  public function orderNotesDatatable( $orderKey )
  {
    return $this->_cDocument->portlet(
      $this->_dTabPortletType,
      $this->_dTabPortletColor,
      $this->_dTabPortletIcon,
      $this->_dTabPortletTitle,'',
      $this->_cDocument->datatable(
        $this->_dTabDatatableID,
        $this->_dTabDatatableCol,
        $this->_formatDatatable($this->_mOrderMessages->getOrderMessages( $orderKey ))
      ),[],[],true
    );
  }

  private function _formatDatatable( $messages )
  {
    foreach( $messages as &$entry ){
      $entry['key']       = sprintf('#%08d',$entry['key']);
      $entry['order-key'] = sprintf('#%08d',$entry['order-key']);
      $entry['message']   = (($entry['message']=="")?'No Message!':$entry['message']);
      $entry['timestamp'] = (($entry['timestamp']=="0000-00-00 00:00:00")?"Bad Entry":date("l jS \of F Y h:i:s A",strtotime($entry['timestamp'])));
    }
    return $messages;
  }

}
