<?php
/**
 * Dashboard
 *
 * @package App\Event\Page\Main
 * @version 1.0.0
 */
namespace App\Event\Main;

/**
 * Main Dashboard
 *
 */

class NotFound extends \App\Event\Page
{
    /**
     * @var string
     */
    public $view = "NotFound";
    /**
     * @var string
     */
    public $title = "404 Page not Found";

    /**
     * @return mixed
     */
    public function build()
    {
        return "";
    }
    /**
     * @param $view
     */
    public function addResources($view)
    {
        $view->addCss("Plugin/font-awesome/css/font-awesome.min.css")
            ->addCss("Plugin/simple-line-icons/simple-line-icons.min.css")
            ->addCss("Plugin/bootstrap/css/bootstrap.min.css")
            ->addCss("Plugin/uniform/css/uniform.default.css")
            ->addCss("Plugin/bootstrap-switch/css/bootstrap-switch.min.css")
            ->addCss("Plugin/select2/css/select2.min.css")
            ->addCss("Plugin/select2/css/select2-bootstrap.min.css")
            ->addCss("Style/Global/components.min.css")
            ->addCss("Style/Global/plugins.min.css")
            ->addCss("Style/Page/error.min.css")
        /** Add script files */
            ->addJavascript("Plugin/jquery.min.js")
            ->addJavascript("Plugin/bootstrap/js/bootstrap.min.js")
            ->addJavascript("Plugin/js.cookie.min.js")
            ->addJavascript("Plugin/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js")
            ->addJavascript("Plugin/jquery-slimscroll/jquery.slimscroll.min.js")
            ->addJavascript("Plugin/jquery.blockui.min.js")
            ->addJavascript("Plugin/uniform/jquery.uniform.min.js")
            ->addJavascript("Plugin/bootstrap-switch/js/bootstrap-switch.min.js")
            ->addJavascript("Plugin/jquery-validation/js/jquery.validate.min.js")
            ->addJavascript("Plugin/jquery-validation/js/additional-methods.min.js")
            ->addJavascript("Plugin/select2/js/select2.full.min.js")
            ->addJavascript("Script/Global/app.min.js");
        $view->addToken('title', $this->title)
            ->addToken('no-follow', $this->nofollow)
            ->addToken('body-class', 'page-404-3');
    }
}
