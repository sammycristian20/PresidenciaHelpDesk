<?php

namespace App\Plugins\Envato\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugins\Envato\Model\Envato;
use App\Plugins\Envato\Model\EnvatoPurchase;
use App\User;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;

class TicketTimeLineController extends Controller
{
    public function showPurcaseCode($thread)
    {
        $purchased = new EnvatoPurchase;
        $purchase  = $purchased->where('ticket_id', $thread->ticket_id)->first();
        if ($purchase) {
            if ($purchase->purchase_code) {
                $color        = 'green';
                $support_date = $purchase->supported_until;
                if ($support_date && \Carbon\Carbon::now()->gt($support_date)) {
                    $color = 'red';
                }
                echo "<tr><td><b>Support Expire</b></td>   <td style=color:" . $color . ";>" . faveoDate($support_date). $this->displayProperties($thread) . "</td></tr>";
            }
        }
    }
    public function displayProperties($thread)
    {
        return "&nbsp;&nbsp;&nbsp;&nbsp;<button class='btn btn-primary btn-xs' id=envato onclick='envato()'>Details</button>
            <script type=text/javascript>
                function envato()
                            {
                            $.ajax({
                            url:'" . url('envato/details') . '/' . $thread->ticket_id . "',
                                    type: 'get',
                                    beforeSend: function() {
    $('.loader1').css('display','block');
                                            $('#gifshow').show();
                                    },
                                    success: function(html) {
                                        $('#gifshow').hide();
                                        $('.loader1').hide();
                                    $('#resultdiv').html(html);
                                    }
                            });
                                    }
            </script>";
    }
    public function getDetails($ticket_id)
    {
        try {
            $response      = "";
            $ticket        = Tickets::whereId($ticket_id)->select('id', 'user_id')->first();
            $purchase_code = EnvatoPurchase::where('ticket_id', $ticket_id)
                    ->value('purchase_code');
            if ($purchase_code) {
                $response = $this->envatoController()
                        ->setPersonalToken()
                        ->verifyPurchaseCode($purchase_code);
            }
            if ($response && is_array($response)) {
                echo "<div class =box><div class =box-header>"
                . "<h3 class = box-title>Envato Product Details</h3>"
//                        ."<div class='box-tools pull-right'>
//                <button type='button' class='btn btn-box-tool' data-widget='remove'><i class='fa fa-times'></i></button>
//              </div>"
                        . "</div><div class =box-body>"
                . "<table class='table table-bordered table-striped' id='example1'><thead><tr>"
                . "<th>Amount</th><th>Sold At</th><th>Name</th></tr></thead><tbody>";
                $this->iterate($response);
                echo "</tbody></table></div></div></div>";
            }
            else {
                echo "<div class =box><div class =box-header>"
                . "<h3 class = box-title>Envato Product Details</h3>"
//                        ."<div class='box-tools pull-right'>
//                <button type='button' class='btn btn-box-tool' data-widget='remove'><i class='fa fa-times'></i></button>
//              </div>"
                        . "</div><div class =box-body>"
                . "<p>Details not available</p></div></div></div>";
            }
        } catch (\Exception $e) {
            echo "<div class =box><div class =box-header>"
            . "<h3 class = box-title>Envato Product Details</h3>"
//                    ."<div class='box-tools pull-right'>
//                <button type='button' class='btn btn-box-tool' data-widget='remove'><i class='fa fa-times'></i></button>
//              </div>"
                    . "</div><div class =box-body>"
            . "<p>" . $e->getMessage() . "</p></div></div></div>";
        }
    }
    public function envatoController()
    {
        return new EnvatoController();
    }
    public function iterate($array)
    {
        if (count($array) > 0) {
            echo "<tr><td>".$array['amount']."</td><td>".faveoDate($array['sold_at'])."</td><td>".$array['item']['name']."</td></tr>";
//            foreach ($array as $key => $value) {
//                if (is_string($value)) {
//                    echo "<tr><td>$key</td><td>$value</td></tr>";
//                }
//            }
        }
    }
}
