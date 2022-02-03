<?php
/**
 * MediaBuyEmail
 *
 * @package App\Event\Request
 * @version 1.0.0
 */
namespace App\Event\Request;

/**
 * MediaBuyEmail
 *
 * This file is in charge of getting mini portlets
 */
class MediaBuyEmail extends \Framework\Event\Base
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
        $userKey = $userKey = \Framework\Core\Patron::getUserKey();
        /** @var get the user id */
        if(\Framework\Core\Patron::hasPermission('super-user')){ $userKey = 0; }
        /** @var get access to the database */
    	$MediaBuyEmailModel = \Framework\Core\Modulus::get('MediaBuyEmail','Holylandhealth');
        try {
            switch ($_POST['action']) {
                case 'saveLink':
                    try{
                    $MediaBuyEmailModel->updateEmailLink(
                        $_POST['name'],
                        $_POST['promoter-key'],
                        $_POST['funnel-key'],
                        $_POST['cost'],
                        $_POST['expected-return'],
                        $_POST['start-date'],
                        $_POST['end-date'],
                        $_POST['key']
                    );
                    echo json_encode(array('result'=>true));
                } catch (\Framework\Exceptional\BaseException $e ){
                    echo json_encode(array('result'=>false,'An error has occured while trying to process your request'));
                }
                break;
                case 'getEditData':
                /** @var get all the data at once for an edit */
                    $linkData = $MediaBuyEmailModel->getLinkData($_POST['key']);
                    $promoterData = $MediaBuyEmailModel->getPromoters($userKey);
                    $funnelData = $MediaBuyEmailModel->getFunnels();
                    /** encode the data and return it  */
                    echo json_encode(array( 'result' => true,'link-data' => $linkData, 'promoter-data' => $promoterData,'funnel-data' => $funnelData ));
                break;
                case 'getLinkData':
                    $data = $MediaBuyEmailModel->getLinkData($_POST['key']);
                    echo json_encode(array('result'=>true,'data'=>$data));
                    break;
                case 'getFunnels':
                    $data = $MediaBuyEmailModel->getFunnels();
                    echo json_encode(array('result'=>true,'data'=>$data));
                    break;
            }
        } catch (\Framwork\Core\Exceptional\BaseException $exception) {
            $exception->log();
            echo json_encode(array("result" => false, "message" => "Not implemented!"));
        }
    }
}
