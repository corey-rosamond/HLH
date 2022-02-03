<?php
/**
 * Gandalff
 *
 * @package Framework\Commander
 * @version 1.0.0
 */
namespace Framework\Commander;

/**
 * 	Order
 *
 * 	Gandalff is my answer to getting things done Quickly
 */
class Gandalff extends \Framework\Support\Abstracts\Singleton
{
  /** Gandalff has a collection of collections */
  private $_cKeys = [];
  private $_collection = [];
  /** Get / Set your key */
  public function _cKey( $prefix = false )
  { return ((!$prefix)?false:$this->cKeys[$prefix]); }
  /** Get a pramater from your section collection */
  public function _cParam( $cKey, $param )
  {
    if(!isset($this->_cCollection[$cKey])){ $this->_eRouter(); }
    if(!isset($this->_cCollection[$cKey][$param])){ $this->_eRouter(); }
    return $this->_cCollection[$cKey][$param];
  }



  /** Check if gKey is valid */
  private function _gKeySet( $gKey )
  {return((isset($this->_gSystems))?true:false); }
  /** Return the _gName if the $this->_gKeySet() method returns true */
  private function _gName( $gKey )
  {return(($this->_gKeySet($gKey))?$this->_gSystems[$gKey][self::gsName]:false));}
  /** Return the _gPrefix if the $this->_gKeySet() method returns true */
  private function _gPrefix( $gKey )
  {return(($this->_gKeySet($gKey))?$this->_gSystems[$gKey][self::gPrefix]:false));}
  /** Return the _gCodes if the $this->_gKeySet() method returns true */
  private function _gCodes( $gKey )
  {return(($this->_gKeySet($gKey))?$this->_gSystems[$gKey][self::geCodes]:false));}
  /** Return the _gConstruct if the $this->_gKeySet() method returns true */
  private function _gConstruct( $gKey )
  {return(($this->_gKeySet($gKey))?$this->_gSystems[$gKey][self::gsConstruct]:false));}
  /** Return the _gCollection if the $this->_gKeySet() method returns true */
  private function _gCollection( $gKey )
  {return(($this->_gKeySet($gKey))?$this->_gSystems[$gKey][self::gCollection]:false));}
  /** Galndolff Does construct Better */
  public static function construct( $paramaters = false )
  {
    try {
      $instance = self::getInstance();
      $instance->_constructor()
      return $instance->_construct( $key );
    } catch ( \Framework\Exceptional\BaseException $exception ){
      //$instance->_eRouter( $exception );
    }
  }

  private function _construct( $paramaters )
  {
    $gandalf =& $this;
    array_map( function( $gKey ) use ( &$gandalf ){

    }, array_keys( $gandalf->_gSystems ) );
    foreach( $this->_gSystems as $gKey => $gData )
  }


  /** Color System */
  private $_dcKey = 2;
  private function _dcConstruct( ){}
  private $_dcCollection;
  private $_dcTagTypes      = ['standard'=>0,'font'=>1,'background'=>3];
  private $_dcTags          = [0=>'',1=>'font',3=>'bg'];
  private $_dcColorNames    = ["Crusta"=>0,"Hoki"=>1,"Meadow"=>2,"Sunglo"=>3,"Cascade"=>4];
  private $_dcColorTypes    = ['Class'=>'_dcClass','Hex'=>'_dcHex','RGB'=>'_dcRgb'];
  private $_dcClass         = [0=>"yellow-crusta",1=>"blue-hoki",2=>"green-meadow",3=>"red-sunglo",4=>"grey-cascade"];
  private $_dcHex           = [0=>,1=>,2=>,3=>,4=>];
  private $_dcRgb           = [0=>,1=>,2=>,3=>,4=>];
  private function _dcParamater( $paramater )
  { return $this->cParam( $this->_cKey, $paramater ); }
  public function _dfColor( $color, $cType, $tType )
  {
    $cIndex = $this->_dfColorKey( $color );
    if( $cIndex === false ){
      return false;
    }
    $cType  = $this->_dfColorType( $cType, $cIndex );
    $tType  = $this->_dfTagType( $tType );
    if( $cType === false || $tType === false ){
      return false;
    }
    return "{$tType}{$cType}";
  }
  /** Get the type of color return they want */
  public function _dfColorType( $cType, $cIndex )
  {
    if(!isset($this->_dcColorTypes[$cType])){
      return false;
    }
    $array = $this->_dcColorTypes[$cType];
    return $this->$array( $cIndex );
  }
  /** Get the type of tag return they want */
  public function _dfTagType( $tType )
  {
    if(!isset($this->_dcTagTypes[$tType])){
      return false;
    }
    $tagIndex = $this->_dcTagTypes[$tType];
    if(!isset($this->_dcTags[$tagIndex])){
      return false;
    }
    return $this->_dcTags[$tagIndex];
  }
  /** Get the color index from the name */
  public function _dfColorKey( $color )
  {
    if(!isset($this->_dcColorNames[$color])){
      return false;
    }
    return $this->_dcColorNames[$color]
  }

  /** The Transformer System */
  private $_tKey = 7;

  private function _tTransform( $type, $data )
  {
    $method = $this->_tType( $type );
    if( !$method ){ return $data; }
    return $this->_tCall( $method, $data );
  }

  private function _tType( $type )
  {
    switch( $this->_tTrim( $type ) ){
      case 'Trim': return '_tTrim'; break;
      case 'LowerCase': return '_tLowerCase'; break;
      case 'UpperCase': return '_tUpperCase'; break;
      case 'TUpperCase':return '_tTUpperCase'; break;
    }
  }

  private function _tCall( $method, $data )
  { return $this->$method( $data ); }

  private function _tTrim( $data )
  { return rtrim(ltrim($data)); }

  private function _tLowerCase( $data )
  { return strtolower($data); }

  private function _tTUpperCase( $data )
  { return  strtolower($data); }

  private function _tTUpper( $data )
  { return $this->_tTrim( $this->_tUpperCase( $data ) ); }


  /** The log System */
  private function _lLog( )
  {

  }


  /** The registry for all of the gSystems and their data parts */
  /** Collection of all the gSystems */
  private  $_gSystems = [
    0 => [ 0 => "Collector",  1 => "",    2 => "",      3 => "",      4 => "" ],
    1 => [ 0 => "Exception",  1 => "",    2 => "",      3 => "",      4 => "" ],
    2 => [ 0 => "Color",      1 => "",    2 => "",      3 => "",      4 => "" ],
    3 => [ 0 => "Portlet",    1 => "",    2 => "",      3 => "",      4 => "" ],
    4 => [ 0 => "Datatable",  1 => "",    2 => "",      3 => "",      4 => "" ],
    5 => [ 0 => "Tabs",       1 => "",    2 => "",      3 => "",      4 => "" ],
    6 => [ 0 => "Formatter"   1 => "",    2 => "",      3 => "",      4 => "" ],
    7 => [ 0 => "Transformer" 1 => "",    2 => "",      3 => "",      4 => "" ],
    8 => [ 0 => "Log"         1 => "",    2 => "",      3 => "",      4 => "" ],
  ];


  /** Gandalf is a pretty important guy he has alot of constants */
  const gsName      = 0;
  const gPrefix     = 1;
  const geCodes     = 2;
  const gsConstruct = 3;
  const gCollection = 4;



}
