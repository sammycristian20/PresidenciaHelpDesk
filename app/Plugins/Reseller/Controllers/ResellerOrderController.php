<?php

namespace App\Plugins\Reseller\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Reseller\Controllers\ResellerController;
use App\User;
use App\Plugins\Reseller\Model\Reseller;

class ResellerOrderController extends Controller {

    public function __construct() {
        $club = new ResellerController;
        $reseller = new Reseller;
        $this->club = $club;
        $this->reseller = $reseller->where('id', '1')->first();
    }

    public function ResellerOrderButton($user) {
        
        return "<button class='btn btn-primary' id=reseller onclick=reseller(" . $user . ")>Show All Orders</button>
            <script type=text/javascript>
                function reseller(id)
                            {
                                var id = id;
                            $.ajax({
                            url:'../order-search/'+id,
                                    type: 'get',
                                    beforeSend: function() {
    $('.loader1').css('display','block');
                                            $('#gifshow').show();
                                    },
                                    success: function(html) {
                                        $('.loader1').hide();
                                        $('#gifshow').hide();
                                    $('#resultdiv').html(html);
                                    }
                            });
                                    }
            </script>";
        
    }

    /**
     * Search for the order
     * @param type $id
     * @param \App\Http\Controllers\plugin\resellerclub\User $user
     */
    public function getSingleDomain($id, User $user) {
        $user = $user->where('id', $id)->first();
        //dd($user);

        if ($user->customerid) {

            $products = array();
            $singleus = $this->club->getsingleDomain_LinuxUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
            
            if (is_array($singleus)) {
                if (array_key_exists('1', $singleus)) {
                    $products = array_merge($products, $singleus);
                }
                $singleuk = $this->club->getsingleDomain_LinuxUK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singleuk)) {
                    $products = array_merge($products, $singleuk);
                }
                $singlein = $this->club->getsingleDomain_LinuxIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                //dd($singlein['1']);
                if (array_key_exists('1', $singlein)) {
                    $products = array_merge($products,$singlein);
                }
                $singlehk = $this->club->getsingleDomain_LinuxHK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singlehk)) {
                    $products = array_merge($products, $singlehk);
                }
                $singletr = $this->club->getsingleDomain_LinuxTR($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singletr)) {
                    $products = array_merge($products, $singletr);
                }

                $webservice = $this->club->getWebservices($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $webservice)) {
                    $products = array_merge($products, $webservice);
                }
                $singleWindowsus = $this->club->getsingleDomain_WindowsUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singleWindowsus)) {
                    $products = array_merge($products, $singleWindowsus);
                }

                $singleWindowsuk = $this->club->getsingleDomain_WindowsUK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singleWindowsuk)) {
                    $products = array_merge($products, $singleWindowsuk);
                }
                $singleWindowsin = $this->club->getsingleDomain_WindowsIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singleWindowsin)) {
                    $products = array_merge($products, $singleWindowsin);
                }
                $singleWindowshk = $this->club->getsingleDomain_WindowsHK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singleWindowshk)) {
                    $products = array_merge($products,$singleWindowshk);
                }
                $singleWindowstr = $this->club->getsingleDomain_WindowsTR($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $singleWindowstr)) {
                    $products = array_merge($products, $singleWindowstr);
                }
                $multiLinuxus = $this->club->getmultiDomain_LinuxUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiLinuxus)) {
                    $products = array_merge($products, $multiLinuxus);
                }
                $multiLinuxuk = $this->club->getmultiDomain_LinuxUK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiLinuxuk)) {
                    $products = array_merge($products, $multiLinuxuk);
                }
                $multiLinuxin = $this->club->getmultiDomain_LinuxIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiLinuxin)) {
                    $products = array_merge($products, $multiLinuxin);
                }
                $multiLinuxhk = $this->club->getmultiDomain_LinuxHK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiLinuxhk)) {
                    $products = array_merge($products,$multiLinuxhk);
                }
                $multiLinuxtr = $this->club->getmultiDomain_LinuxTR($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiLinuxtr)) {
                    $products = array_merge($products, $multiLinuxtr);
                }
                $multiWindowsus = $this->club->getmultiDomain_WindowsUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiWindowsus)) {
                    $products = array_merge($products, $multiWindowsus);
                }
                $multiWindowsuk = $this->club->getmultiDomain_WindowsUK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiWindowsuk)) {
                    $products = array_merge($products, $multiWindowsuk);
                }
                $multiWindowsin = $this->club->getmultiDomain_WindowsIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiWindowsin)) {
                    $products = array_merge($products, $multiWindowsin);
                }
                $multiWindowshk = $this->club->getmultiDomain_WindowsHK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiWindowshk)) {
                    $products = array_merge($products, $multiWindowshk);
                }
                $multiWindowstr = $this->club->getmultiDomain_WindowsTR($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $multiWindowstr)) {
                    $products = array_merge($products, $multiWindowstr);
                }
                $resellerLinuxus = $this->club->getReseller_LinuxUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerLinuxus)) {
                    $products = array_merge($products, $resellerLinuxus);
                }
                $resellerLinuxuk = $this->club->getReseller_LinuxUK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerLinuxuk)) {
                    $products = array_merge($products, $resellerLinuxuk);
                }
                $resellerLinuxin = $this->club->getReseller_LinuxIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerLinuxin)) {
                    $products = array_merge($products,$resellerLinuxin);
                }
                $resellerLinuxtr = $this->club->getReseller_LinuxTR($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerLinuxtr)) {
                    $products = array_merge($products, $resellerLinuxtr);
                }
                $resellerWindowsus = $this->club->getReseller_WindowsUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerWindowsus)) {
                    $products = array_merge($products, $resellerWindowsus);
                }
                $resellerWindowsuk = $this->club->getReseller_WindowsUK($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                //dd($resellerWindowsuk);
                if (array_key_exists('1', $resellerWindowsuk)) {
                    $products = array_merge($products, $resellerWindowsuk);
                }
                $resellerWindowsin = $this->club->getReseller_WindowsIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerWindowsin)) {
                    $products = array_merge($products, $resellerWindowsin);
                }
                $resellerWindowstr = $this->club->getReseller_WindowsTR($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $resellerWindowstr)) {
                    $products = array_merge($products, $resellerWindowstr);
                }
                $VPSus = $this->club->getVPS_LinuxUS($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $VPSus)) {
                    $products = array_merge($products, $VPSus);
                }
                $VPSin = $this->club->getVPS_LinuxIN($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $VPSin)) {
                    $products = array_merge($products, $VPSin);
                }
                $enterpriseEmail = $this->club->EnterpriseEmail($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $enterpriseEmail)) {
                    $products = array_merge($products, $enterpriseEmail);
                }
                $businessEmail = $this->club->BusinessEmail($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $businessEmail)) {
                    $products = array_merge($products, $businessEmail);
                }
                $dedicatedServer = $this->club->DedicatedServer($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $dedicatedServer)) {
                    $products = array_merge($products, $dedicatedServer);
                }
                $managedServer = $this->club->ManagedServer($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $managedServer)) {
                    $products = array_merge($products, $managedServer);
                }
                $siteLock = $this->club->SiteLock($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $siteLock)) {
                    $products = array_merge($products, $siteLock);
                }
                $codeGuard = $this->club->CodeGuard($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $codeGuard)) {
                    $products = array_merge($products, $codeGuard);
                }
                $getDomainReg = $this->club->GetDomainReg($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $getDomainReg)) {
                    $products = array_merge($products, $getDomainReg);
                }
                $ssl = $this->club->Ssl($this->reseller->userid, $this->reseller->apikey, $user->customerid);
                if (array_key_exists('1', $ssl)) {
                    $products = array_merge($products, $ssl);
                }
            }
            else 
            {
                echo "<div class =box>";
                echo "<div class =box-header>";
                echo "<h1 class = box-title style='color:red'>Sorry! Not able to connect to Resellerclub ! Please check your Connection .</h1>";
                echo "</div>";
                echo "<div class =box-body>";
                echo "</div>";
                echo "</div>";     
            }
            
            
            echo "<div class =box>";
            echo "<div class =box-header>";
            echo "<h3 class = box-title>Reseller Product List</h3>";
            echo "</div>";
            echo "<div class =box-body>";
            echo "<table class='table table-bordered table-striped' id='example1'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Domain Name</th>";
            echo "<th>Product Name</th>";
            echo "<th>Expiry</th>";
            echo "<th>Status</th>";
            echo "<th>Customer ID</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            //dd($products);
            if(count($products)>0){
            for ($i=0;$i<$products['recsonpage'];$i++) {

                echo "<tr>";
                echo "<td>" . $products[$i]['entity.description'] . "</td>";
                echo "<td>" . $products[$i]['entitytype.entitytypename'] . "</td>";
                if (array_key_exists('orders.endtime', $products[$i])) {
                    echo "<td>" . date('Y-m-d', $products[$i]['orders.endtime']) . "</td>";
                } else {
                    echo "<td>---</td>";
                }


                echo "<td>" . $products[$i]['entity.currentstatus'] . "</td>";
                echo "<td>" . $products[$i]['entity.customerid'] . "</td>";
                //echo "<td>".$values."<td>";
                echo "</tr>";
            }
            }else{
                echo "<tr><td>$user->first_name $user->last_name has no orders</td></tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class =box>";
            echo "<div class =box-header>";
            echo "<h1 class = box-title style='color:red'>Reseller Customer id is not available</h1>";
            echo "</div>";
            echo "<div class =box-body>";
            echo "</div>";
            echo "</div>";
        }
    }

}
