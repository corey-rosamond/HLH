<?
/**
 * DataTransferObject
 *
 * @package Framework\Support\Object\DataTransferObject
 * @version 1.0.0
 */
namespace Framework\Support\Object\DataTransferObject;

/**
 * DataTransferObject
 *
 * Basic object for holding data
 */
class DataTransferObject extends \ArrayAccess{
{
  /**
   * Set the variable
   * @method  __set
   * @param   mixed $name   Variable Name
   * @param   mixed $value  Value
   * @access  public
   */
  public function  __set($name, $value)
  {
    $this[$name] = $value;
  }

  /**
   * Get the requested variable or false
   * @method  __get
   * @param   mixed  $name  Variable name
   * @return  mixed         Variable value or false
   * @access  public
   */
  public function  __get($name)
  {
    if( !isset($this[$name]) ){
      return false;
    }
    return $this[$name];
  }
}
