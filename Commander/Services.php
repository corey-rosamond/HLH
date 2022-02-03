<?php

namespace Framework\Commander;

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."Service".DSEP."ServiceToken.php");
class Services extends \Framework\Support\Abstracts\Singleton
{
  private $_mService;

  /** TYPE CONSTANTS */
  const email             = 0;
  const paymentProcessor  = 1;
  const fulfillment       = 2;
  const addressValidation = 3;

  /**
   * @var array Service types
   */
  private $_types = [0 => 'Email', 1 => 'Payment', 2 => 'Fulfillment', 3 => 'Address Validation'];


  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_mService = \Framework\Core\Receptionist::modal( "Service", "Holylandhealth", true );
    return $instance;
  }

  public function get( $key )
  {
    $data = $this->_mService->get( $key );
    $data['configuration'] = json_decode($data['configuration'],true);
    return $this->_makeObject($data);
  }

  private function _makeObject($data)
  {
    switch($data['type']){
      case self::email:
        /** Email tokens **/
        \Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."Email".DSEP."EmailToken.php");
        return new \Framework\Objectify\EmailToken($data['key'], $data['name'], $data['type'], $this->_types[$data['type']], $data['configuration']);
      break;
      case self::paymentProcessor:
        /** Support payment tokens */
        \Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."PaymentProcessor".DSEP."PaymentProcessorToken.php");
        return new \Framework\Objectify\PaymentProcessorToken($data['key'], $data['name'], $data['type'], $this->_types[$data['type']], $data['configuration']);
      break;
      case self::fulfillment:
        /** support fulfillment tokens */

      break;
      case self::addressValidation:
        /** support address validation tokens */
        \Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."AddressValidation".DSEP."AddressValidationToken.php");
        return new \Framework\Objectify\AddressValidationToken($data['key'], $data['name'], $data['type'], $this->_types[$data['type']], $data['configuration']);
      break;
    }
  }
}
