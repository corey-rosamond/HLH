

​
​
   /**
    * The initial public construct.  Similar to what is used in controller.php
    * @method construct
    * @param  emailToken The email token
    * @return Email An instance of the email controller
    */

   public static function construct( $emailToken )
   {
​
     try {
       $instance = self::getInstance();
       return $instance->_construct( $emailToken );
     }
     catch ( \Framework\Exception\BaseException $exception) {
         $instance->_eRouter( $exception );
       }
​
   }
​
   private function _construct( $emailToken )
   {
    //  require_once('lib/swift_required.php');
     if (is_null($emailToken) || strlen($emailToken) < 0) {
       throw new Exception("Missing email token");
     }
​
     //Now that we creating an instance of Email, lets get Swiftmail setup.
     //If something bad happens, we can return an error on the construct...
     require_once 'lib/swift_required.php';
     $this->emailToken = $emailToken;
     $this->useToken();
  }
​
  public function setTransport($host, $port, $user, $pass)
  {
    $this->transport = Swift_SmtpTransport::newInstance($host, $port)
    ->setUsername($user)
    ->setPassword($pass)
    ;
    return true;
  }
​
  /**
   * Takes the information in the email token that was passed to the
   * constructor.  Per Corey, that information is an assoc array with
   * the to, from, subject, body, etc.  This will parse that out into an
   * instance of this Email object
   * @return boolean True if successful
   */
  private function useToken() {
    $this->setFromAddress($this->emailToken["from"]);
    $this->setRecipient($this->emailToken["to"]);
    $this->setSubject($this->emailToken["subject"]);
    $this->setBody($this->emailToken["body"]);
  }
​
  private $_isLocked = false;

  private function lock()
  { $this->_isLocked = true }

  private function unlock()
  { $this->_isLocked = false }

  public function fromAddress( $fromAddress = false )
  {
    if( !$fromAddress ){
      return $this->_mFromAddress;
    }
    return $this->_mFromAddress = $fromAddress;
  }

  public function setRecipient($toAddress) {
    $this->_mEmail = $toAddress;
  }
​
  public function setBody($body) {
    $this->_mBody = $body;
  }
​
  public function setSubject($subject) {
    $this->_mSubject = $subject;
  }
​







  public function sendMessage($server = false)
  {
      if ($server) {
        //TODO: Write the code to handle if server is populated
        $username = $this->ServerUsername;
        $password = $this->ServerPassword;
        $from = 'Funnel Server';
      }
      else {
        //TODO: This may not be useful anymore since we are using transport
        $username = $this->username;
        $password = $this->password;
        $from = $this->from;
      }
​
      /*
      //Use the transport agent from Swift
      $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
        ->setUsername($username)
        ->setPassword($password);
      */
      //mailer
​
      $mailer = Swift_Mailer::newInstance($this->transport);
      //Build the message
      $message = Swift_Message::newInstance($this->_mSubject)
        ->setFrom(array($this->_mFromAddress => $this->companyName))
        ->setTo(array($this->_mEmail))
        ->setBody($this->_mBody);
      //Send the email
      $mailer->sendMessage($message);
      //return true;
      echo("It worked!!!2");
      return true;
  }
​
​
​
​
​
  public function lessOldsendMessage($to, $subject, $message, $server=false)
  {
    if ( $server ) {
      //TODO: Write the code to handle if server is populated
      $username = $this->ServerUsername;
      $password = $this->ServerPassword;
      $from = 'Funnel Server';
    }
    else {
      $username = $this->username;
      $password = $this->password;
      $from = $this->from;
    }
    //Use the transport agent from Swift
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl
      ->setUsername($username )
      ->setPassword($password);
    //mailer
    $mailer = Swift_Mailer::newInstance($transport);
    //Build the message
    $message = Swift_Message::newInstance($subject)
      ->setFrom(array($username => $from))
      ->setTo(array($to))
      ->setBody($message,'text/html');
    //Send the email
    $mailer->sendMessage($message);
    //return true;
  }
​
  public function sendTemplate( $template, $data, $to ) {
    //Switch case for template string
​
    /* Corey is going to write a tokenize function.  I am going to pass this stuff into that
    and take its return value and use what it returns.
    */
​
    $this->sendMessage($to, $subject, $message);
    //return true
    }
​
  /**
   * Use Swiftmail to send the email
   * @return boolean
   */
  public function OLDsendMesssage() {
    if (is_null($this->transport)) {
      throw new Exception("Transport has not been set.");
    }
    $message = Swift_Message::newInstance($this->_mSubject)
    ->setFrom($this->_mFromAddress)
    ->setTo($this->_mEmail)
    ->setBody($this->_mBody)
    ;
​
    $result = $this->mailer->send($message);
    //Returns the result to the function caller
    return $result;
  }
​
​
  public function readEmail() {
​
  }
​
  private function _eThrow( $message, $code, $exception )
  {
    /** Check if this is an interger */
    if( is_int( $message ) ){
      /** This is an integer that means it is a message for this sytem get it from the message function */
      $message = $this->_eMessage( $message );
    }
    throw new \Framework\Exceptional\EmailControllerException(
      $message,
      $code,
      (( $exception !== false )? $exception->getCode() : 0),
      (( $exception !== false )? $exception->getFile() : "/Framework/Appliaction/common/Controller/Email.php"),
      (( $exception !== false )? $exception->getLine() : 0),
      (( $exception !== false )? $exception : false )
    );
  }
}
