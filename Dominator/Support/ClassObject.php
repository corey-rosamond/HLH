<?php
/**
 * ClassObject
 *
 * @package Framework\Dominator\Support
 * @version 1.0.0
 */
namespace Framework\Dominator\Support;

/**
 * ClassObject
 *
 * Manages classes for DocumentObjects
 */
class ClassObject{

  /*********************************
  *        OBJECT VARIABLES        *
  /********************************/

  /**
   * Collection of classes for the object it belongs to
   * @var   array $classes
   * @access  private
   */
  private $classes = [];

  /**
   * Count of the classes registered to this object
   * @var     integer $count
   * @access  private
   */
  private $count = 0;

  /*********************************
  *          BASE METHODS          *
  /********************************/

  /**
   * __construct
   * @method  __construct
   * @param   false|array
   * @return  Class
   * @access  public
   */
  public function __construct( $classes = false ){
    /** Check if we were given classes */
    if( $classes ){
      /** Set the classes */
      $this->classes = $classes;
      /** Set the count */
      $this->count = count( $classes );
    }
    /** Done return this */
    return $this;
  }

  /**
   * This is a very high use object make sure we do not have
   * any memory leaks
   * @method  __destruct
   * @return  void
   * @access  public
   */
  public function __destruct(){
    unset( $this->classes );
    unset( $this->count );
  }

  /*********************************
  *  GETTERS / SETTERS / CHECKERS  *
  /********************************/

  /**
   * Add a class
   * @method  addClass
   * @param   string      $name The class to add
   * @access  public
   */
  public function add( $name ){
    /** Make sure we dont have the class */
    if( $this->hasClass( $name ) ){
      /** You cant add something  you already have */
      return false;
    }
    /** Push it into the array */
    array_push( $this->classes, $name );
    /** update the count */
    $this->count++;
    /** return true */
    return true;
  }

  /**
   * Get the index of this class name use for manipulation
   * @method  getIndex
   * @param   string          $name   The class we want the index of
   * @return  intiger|false           False if the class doesent exist otherwise it will be an int
   * @access  private
   */
  private function getIndex( $name ){
    return array_search( $name, $this->classes, true );
  }

  /**
   * Check if class exists
   * @method  hasClass
   * @param   string     $name  The class name to check
   * @return  boolean
   * @access  public
   */
  public function has( $name ){
    return isset( $this->classes[$name] );
  }

  /**
   * Remove a class
   * @method  remove
   * @param   string      $name   The class to remove
   * @return  boolean
   * @access  public
   */
  public function remove( $name ){
    /** Get the index in the array */
    $index = $this->getIndex( $name );
    if( $index === false ){
      /** We couldent find it return false and end the function */
      return false;
    }
    /** Remove it */
    unset( $this->classes[$index] );
    /** Update the count */
    $this->count--;
    /** Reindex the array */
    $this->classes = array_values( $this->classes );
    /** return true */
    return true;
  }

  /*********************************
  *         OUTPUT METhODS         *
  /********************************/

  /**
   * return the string containing the class attribute
   * @method  render
   * @return  string   String containing class attribute
   * @access  public
   */
  public function render(){
    /** Check if we have any classes to render */
    if( $this->count === 0 ){
      /** No classes return empty string */
      return "";
    }
    /** Return the class attribute */
    return ' class="'.implode( " ", array_values( $this->classes ) ).'"';
  }
}
