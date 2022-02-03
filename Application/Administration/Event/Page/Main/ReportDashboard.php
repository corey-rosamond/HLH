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

class ReportDashboard extends \App\Event\Page
{
    /** @var string document title */
    public $title = "Report Dashboard";
    /** @var string Header */
    public $header = "Report Breakdown";
    /** @var string smaller header */
    public $subheader = "great stats at a glance";
    /** @var string this is the html we want the view to output */
    public $Return;
    public function __construct()
    {}
    /**
     * @return mixed
     */
    public function build()
    {
        /** MODELS */
        $this->TrackingModel = \Framework\Core\Modulus::get('Tracking', 'Holylandhealth');
        $this->OrderModel = \Framework\Core\Modulus::get('Orders', 'Holylandhealth');
        /** QUERYS */
        $Campaigns = $this->TrackingModel->getCampaigns();
        $Impressions = $this->TrackingModel->getCampaignImpressions();
        $Sales = $this->OrderModel->ordersPerCampaign();
        // holder for the data
        $this->data = array();

        // Filter the info
        foreach ($Campaigns as $Campaign) {
            $this->data[$Campaign['key']] = [];
            $this->data[$Campaign['key']]['name'] = $Campaign['name'];
            $this->data[$Campaign['key']]['impressions'] = 0;
        }
        foreach ($Impressions as $Impression) {
            $this->data[$Impression['campaign-key']]['impressions'] = $Impression['impressions'];
        }
        foreach ($Sales as $Sale) {
            $this->data[$Sale['campaign-key']]['total-sales'] = $Sale['sales'];
            $this->data[$Sale['campaign-key']]['revenue'] = $Sale['sold'];
        }

        $this->Return .= "
        <div class='portlet box green'>
            <div class='portlet-title'>
                <div class='caption'><i class='fa fa-globe'></i>Tracking Links </div>
                <div class='tools'> </div>
            </div>
            <div class='portlet-body'>
                <table class='table table-striped table-bordered table-hover dt-responsive' width='100%' id='sample_3' cellspacing='0' width='100%'>
                    <thead>
                        <tr>
                            <th >key</th>
                            <th >name</th>
                            <th >impressions</th>
                            <th >total sales</th>
                            <th >total revenue</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($this->data as $key => $row) {
            if (isset($row['name'])) {
                if (!isset($row['total-sales'])) {$row['total-sales'] = '0';}
                if (!isset($row['revenue'])) {$row['revenue'] = '0.00';}
                $this->Return .= "
                <tr>
                    <td>" . $key . "</td>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['impressions'] . "</td>
                    <td>" . $row['total-sales'] . "</td>
                    <td>" . $row['revenue'] . "</td>
                </tr>";
            }
        }

        return $this->Return . "<tbody></table></div></div>";
    }

    /**
     * @param $header
     */
    private function PortletHeader($header, $data)
    {

    }

}
