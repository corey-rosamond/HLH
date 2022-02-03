<?php
/**
 * Upsell3
 *
 * @package App\Event
 * @version 1.2.0
 */
namespace App\Event;

/**
 * Upsell3
 *
 * This is the Checkout event
 */
class Upsell3 extends \App\Abstracts\Event
{
  /** @var int $_eventType */
  protected $_eventType = self::upsell3;

  /**
   * Upsell1 constructor.
   * @method __construct
   * @param $system
   */
  public function __construct($system)
  { return parent::__construct($system); }

  /**
   * Run the event
   * @method run
   */
  public function run()
  {
    /** Run pre Qualify this will make sure we end up where we need to be */
    $this->_preQualify();
    /** @var array $outcome process the cart items */
    $outcome = $this->_cFunnel->processCartAdditions($this->_products);
    /** Check if the outcome is false */
    if(!$outcome['result']){
      /** Check if we have an invalid amount error */
      if(isset($outcome['messages'][1])&&$outcome['messages'][1]=='Invalid amount'){
        /** @var integer $alreadyProcessed Start at 0 lets figure out if they have a balance */
        $alreadyProcessed = 0;
        /** @var array $orderItems Get the array of all the order items */
        $orderItems = $this->_cFunnel->orderItems();
        /** @var integer $product loop through the products being sold on this page */
        foreach($this->_products as &$product){
          /** @var boolean $orderItem Check if the product is in the orderItems */
          $orderItem = $this->_inOrderItems($orderItems, $product);
          /** check if false was returned */
          if($orderItem!==false){
            /** Test if this item has already been reconciled */
            if($orderItem['status']==\Framework\Commander\OrderItems::reconciled){
              /** Count this as a reconciled order */
              $alreadyProcessed++;
            }
          }
        }
        /** Test if all the items for this page have been reconciled */
        if(sizeof($this->_products)==sizeof($alreadyProcessed)){
          /**
           * _completeAndMove will go ahead and complete the event for us
           * including keeping track of the fact that this page type has been completed
           */
          $this->_completeAndMove(self::successNext);
          exit();
        }
        $this->_errors = $outcome['messages'];
        /** Complete the event and move the user to the next action */
        $this->_completeAndMove(self::failureNext);
      }
      /** @var array $_errors Move the messages into the errors array */
      $this->_errors = $outcome['messages'];
      /** Complete the event and move the user to the next action */
      $this->_completeAndMove(self::failureNext);
    }
    /**
     * _completeAndMove will go ahead and complete the event for us
     * including keeping track of the fact that this page type has been completed
     */
    $this->_completeAndMove(self::successNext);
  }
}
