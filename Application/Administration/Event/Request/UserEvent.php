<?php
/**
 * UserEvent
 *
 * @package App\Event\Main
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * UserEvent
 *
 * This runs the user ajax events
 */

class UserEvent extends \Framework\Event\Base
{
    /**
     * @var mixed
     */
    public $requiresLogin = false;
    /**
     * @return mixed
     */
    public function __construct()
    {return $this;}
    public function run()
    {
      try {
      switch ($_POST['action']) {
          case 'forgot':
            echo json_encode(array("result" => false, "message" => "Not implemented!"));
            break;
          case "login":
            /** Ask patron to log them in*/
            $result = \Framework\Core\Patron::login($_POST['username'], $_POST['password'], $_POST['remember']);
            /** return the result */
            echo json_encode($result);
            break;
          case 'logout':
            $result = \Framework\Core\Patron::logout();
            echo json_encode($result);
            break;
      }
      } catch (\Framwork\Core\Exceptional\BaseException $exception) {
          $exception->log();
          echo json_encode($exception);
      }
    }
}
