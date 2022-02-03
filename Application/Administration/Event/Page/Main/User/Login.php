<?php
/**
 * Login
 *
 * @package App\Event\Main
 * @version 1.0.0
 */
namespace App\Event\Page\Main\User;

/**
 * Login
 *
 * This is the user login page
 */
class Login extends \App\Abstracts\Page
{
  /****************************************************/
  /*                  PAGE SETTINGS                   */
  /****************************************************/
  /** @var boolean $buildui Tell the main page object that we don't want the ui */
  protected $_dBuildUserInterface = false;
  /** @var boolean $requiresLogin */
  public static $requiresLogin = false;
  /** @var array $scripts Array of script files we need framework to include */
  protected $_pageScripts = [["Main.User.login.js", self::application]];


  /**
   * This will build the body of the docment out the rest of the sections of the
   * page will be handled by the Application\Page abstract or by the
   * Advent\Page Abstract
   * @method  body
   * @param   array   $paramaters   This is an optional array of paramtars that can be passed
   * @return  string                The html needed to render the body for this page
   * @access  public
   */
  public function body( array $paramaters = array() ){
    $this->addStyle( "login.css", self::application );
    $this->setDocumentTitle( 'HolylandHealth: Login' );
    $this->setBodyClass( "login" );
    $this->writeBody( $this->loginForm( $this->tokens ) );
  }

  /**
   * Build the login form
   * @method loginForm
   * @param  array $token 
   * @return string
   * @access private
   */
  private function loginForm( $token){
    return "
      <div class='menu-toggler sidebar-toggler'></div>
      <div class='logo'><img src='{$token['image-directory']}hlhlogo.png' alt='' /></div>
        <div class='content'>
          <form class='login-form' action='' method='post' autocomplete='off' >
            <h3 class='form-title font-green'>Sign In</h3>
            <div class='alert alert-danger display-hide'>
              <button class='close' data-close='alert'></button>
              <span> Enter any username and password. </span>
            </div>
            <div class='form-group'>
              <label class='control-label visible-ie8 visible-ie9'>Username</label>
              <input class='form-control form-control-solid placeholder-no-fix' type='text' autocomplete='off' placeholder='Username' name='username' /> </div>
            <div class='form-group'>
              <label class='control-label visible-ie8 visible-ie9'>Password</label>
              <input class='form-control form-control-solid placeholder-no-fix' type='password' autocomplete='off' placeholder='Password' name='password' /> </div>
            <div class='form-actions'>
              <button type='submit' class='btn green uppercase'>Login</button>
              <label class='rememberme check'>
                <input type='checkbox' name='remember' value='1' />Remember </label>
              <a href='javascript:;' id='forget-password' class='forget-password'>Forgot Password?</a>
            </div>
          </form>
          <form class='forget-form' action='index.html' method='post'>
            <h3 class='font-green'>Forget Password ?</h3>
            <p> Enter your e-mail address below to reset your password. </p>
            <div class='form-group'>
              <input class='form-control placeholder-no-fix' type='text' autocomplete='off' placeholder='Email' name='email' />
            </div>
            <div class='form-actions'>
              <button type='button' id='back-btn' class='btn btn-default'>Back</button>
              <button type='submit' class='btn btn-success uppercase pull-right'>Submit</button>
            </div>
          </form>
        </div>
      <footer class='copyright'> 2015 &copy; HolylandHealth LLC. </footer>
      <input type='hidden' id='resources' name='resources' value='' />
    ";
  }
}

