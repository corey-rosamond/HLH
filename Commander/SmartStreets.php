<?php

namespace Framework\Commander;

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Objectify".DSEP."Email".DSEP."EmailMessage.php");
class SmartStreets extends \Framework\Support\Abstracts\Singleton
{

  private $_errorCodes = [
    'AA' => "City, State and Zip code are valid",
    'A1' => "ZIP+4 not matched; address is invalid. (City/state/ZIP + street don't match.)",
    'BB' => "ZIP+4 matched; confirmed entire address; address is valid.",
    'CC' => "Confirmed address by dropping secondary (apartment, suite, etc.) information",
    'F1' => "Matched to military address",
    'G1' => "Matched to general delivery address (e.g., General Delivery WLS Bridge NY 13859)",
    'M1' => "Primary number (e.g., house number) is missing.",
    'M3' => "Primary number (e.g., house number) is invalid.",
    'N1' => "Address missing secondary number (apartment, suite, etc.)",
    'P1' => "PO, RR, or HC box number is missing. (e.g., RR 5 Cadiz KY)",
    'P3' => "PO, RR, or HC box number is invalid.",
    'RR' => "Confirmed address with private mailbox (PMB) info",
    'R1' => "Confirmed address without private mailbox (PMB) info",
    'U1' => "Matched a unique ZIP Code"
  ];

  public static function construct()
  {
    $instance = self::getInstance();
    return $instance;
  }

  public function validateAddress($token, $streetOne, $streetTwo, $city, $state, $zip)
  {
    $errors   = [];
    $street   = $streetOne.(($streetTwo=='')?'':" {$streetTwo}");
    if($street==""||$city==""||$zip==""){
      array_push($errors, "Address failed validation!");
      return ['result'=>false,'messages'=>$errors];
    }
    $street   = urlencode($street);
    $city     = urlencode($city);
    $state    = urlencode($state.", ");
    $zip      = urlencode($zip);
    $authID   = urlencode($token->authenticationID());
    $authToken= urlencode($token->authenticationToken());
    $request  = "{$token->address()}street-address/?street={$street}&city={$city}&state={$state}&zipcode=&candidates=3{$zip}&auth-id={$authID}&auth-token={$authToken}";
    $response = file_get_contents($request);
    $result   = json_decode($response,true);
    if(!isset($result[0])){
      array_push($errors, "Address failed validation!");
      return ['result'=>false,'messages'=>$errors];
    }
    $analysis = $result[0]['analysis'];
    $matchcode = $analysis['dpv_match_code'];
    if($matchcode=='Y'){
      return ['result'=>true,'messages'=>$errors];
    }
    if($matchcode=='D'){
      $ssErrors   = str_split($analysis['dpv_footnotes'], 2);
      foreach($ssErrors as $code){
        if(isset($this->_errorCodes[$code])){
          array_push($errors, $this->_errorCodes[$code]);
        }
      }
      return ['result'=>false,'messages'=>$errors];
    }
    array_push($errors, "Address failed validation!");
    return ['result'=>false,'messages'=>$errors];
  }
}
