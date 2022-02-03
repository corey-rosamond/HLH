<?php
/**
 * System
 *
 * @package App\Event\Management\Users
 * @version 1.2.0
 */
namespace App;

/**
 * System
 *
 * System is the main entry point for all apps built on the framework
 */
\Framework\_IncludeCorrect( FRAMEWORK_ROOT."Advent".DSEP."Page.php" );
\Framework\_IncludeCorrect(APP_ROOT."Abstracts".DSEP."Page.php");
\Framework\_IncludeCorrect(APP_ROOT."Objects".DSEP."MegaMenu.php");
\Framework\_IncludeCorrect(APP_ROOT."Objects".DSEP."Navigation.php");
\Framework\_IncludeCorrect(APP_ROOT."Objects".DSEP."Header.php");
\Framework\_IncludeCorrect(APP_ROOT."Objects".DSEP."QuickSidebar.php");
\Framework\_IncludeCorrect(APP_ROOT."Objects".DSEP."Footer.php");
class System extends \Framework\Support\Abstracts\Singleton
{
  private $Advent;
  public static function initialize(){
    $instance = self::getInstance();
    $instance->Advent = \Framework\Core\Advent::construct();
    $instance->Advent->run();
  }
}
