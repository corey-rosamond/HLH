<?php
/**
 * Portlet
 *
 * @package App\Event\Request
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * Portlet
 *
 * This file is in charge of getting mini portlets
 * @todo add logging for requesting portlest you do not have permissions for
 */
class Portlet extends \Framework\Event\Base
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
        /** Check to see if they specified a portlet to load */
        if (!isset($_GET['load'])) {
            /** no portlet requested 404 return false to end the method */
            header("HTTP/1.0 404 No portlet requested!");
            return false;
        }
        /** they asked for a portlet lets see if it exists */
        if (!file_exists(APP_ROOT . 'Portlets' . DSEP . $_GET['load'] . '.php')) {
            /** it doesent exits error it out and end the method */
            header("HTTP/1.0 404 We are sorry but the " . $_GET['load'] . " portlet does not exist!");
            return false;
        }
        /** ok the portlet exists lets make sure they have permissions to view it */
        try {
            /** @var string this is the class name of the portlet with namespace */
            $portletName = "\\App\\Portlets\\" . $_GET['load'];
            /** @var string this is the permission required to load this portlet */
            $permission = $portletName::$requiredPermission;
            if ($permission) {
                /** this portlet requires a permission make sure they are logged in */
                if (!\Framework\Core\Patron::isLoggedIn()) {
                    header("HTTP/1.0 404 The asset you requested is restricted. Your ip address has been logged!");
                    return false;
                }
                /** if you are here they are logged in check that they have the right permission */
                if (!\Framework\Core\Patron::hasPermission($permission)) {
                    header("HTTP/1.0 404 We are sorry but you do not have the required permissions to use this resource. If you believe this is an error please contact tech support.");
                    return false;
                }
                /** if you are here they have the required permission to view use the portlet run it */
            }
            $portletName::run();
            return true;
        } catch (\Framework\Exceptional\BaseException $e) {
            /** an error occured dont hang the user up but let them know and end the method after logging */
            $e->log();
            header("HTTP/1.0 404 There was an error trying to handle your request Tech Support has been notified!");
            return false;
        }
        header("HTTP/1.0 404 " . $_GET['load']);

    }

}
