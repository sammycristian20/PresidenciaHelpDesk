<?php

namespace App\Plugins\Envato\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Form;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use App\Plugins\Envato\Model\Envato;
use App\Plugins\Envato\Model\EnvatoPurchase;
use App\User;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use DateTime;
use DateTimeZone;
use App\Model\helpdesk\Ticket\Tickets;
use App\Plugins\Envato\Controllers\EnvatoAuthController;

class TicketFormController extends EnvatoAuthController
{
    public function ClientForm($event)
    {
        //dd('event',$event->event);
        $array = json_decode($event->event, true);
        $this->CreateTables();
        $model = new Envato;
        $model = $model->where('id', '1')->first();
        if ($model) {
            $purchase_code = collect($array)->where('unique', '=', 'purchase_code')->all();
            if ($model->mandatory == '1' && !$purchase_code) {
                $prchase_code_form = [
                    'title'                      => 'Purchase Code',
                    'type'                       => 'text',
                    'agentRequiredFormSubmit'    => false,
                    'customerDisplay'            => true,
                    'customerRequiredFormSubmit' => true,
                    'default'                    => 'yes',
                    'unique'                     => 'purchase_code',
                    'agentlabel'                 => 'Purchase Code',
                    'clientlabel'                => 'Purchase Code',
                ];
                $new_array         = array_merge($array, [$prchase_code_form]);

                return $new_array;
            }
        }
    }
    public function AgetTimeLineForm($ticket)
    {
        $user      = new User;
        $userid    = $ticket->user_id;
        $user      = $user->where('id', $userid)->first();
        $email     = $user->email;
        //dd($email);
        $purchased = new EnvatoPurchase;
        $purchased = $purchased->where('username', $email)->first();
        if ($purchased) {
            $code = $purchased->purchase_code;
        }
        else {
            $code = '';
        }
        $model = new Envato;
        $model = $model->where('id', '1')->first();
        if ($model) {
            if ($model->mandatory == '1') {
                echo "<div class='form-group'>
                            <div class='row'>
                                <div class='col-md-2'>
                                    <label>Purchase Code</label> <span class='text-red'> *</span>
                                </div>
                                <div class='col-md-10'>
                                  <input type=text name=purchase_code class=form-control style=width:55% value=" . $code . " disabled>  
                                </div>
                            </div>
                        </div>";
            }
        }
    }
    public function postPurchase($formdata, $username, $source)
    {
        if ($source != 1 && $source != 3) return 0;
        if ($this->isMandatory()) {
            $v = \Validator::make($formdata, ['purchase_code' => 'required']
            );
            if ($v->fails()) {
                throw new \Exception('Purchase Code Required');
            }
        }
        $purchase_code = checkArray('purchase_code', $formdata);
        $support_until = "";
        $sold          = "";
        if ($purchase_code) {
            $envato_ctrl   = new EnvatoController();
            $purchased     = new EnvatoPurchase;
            $response      = $envato_ctrl
                    ->setMandatory($this->is_mandatory)
                    ->setPersonalToken()
                    ->verifyPurchaseCode($purchase_code);
            $support_until = checkArray('supported_until', $response);
            $sold          = checkArray('sold_at', $response);
        }
        $support_date = null;
        if ($support_until) {
            $support_date = carbon($support_until);
        }

        if ($sold) {
            $sold_date = carbon($sold);
            $license   = checkArray('license', $response);
            if (!$this->allowExpired() && $support_date && \Carbon\Carbon::now()->gt($support_date)) {
                throw new \Exception('Your Support has expired for this product! Please renew');
            }
            $purchased->create(['purchase_code'   => $purchase_code,
                'username'        => $username,
                'sold_at'         => $sold_date,
                'supported_until' => $support_date,
                'licence'         => $license
            ]);
        }
    }
    public function CreateTables()
    {
        if (!Schema::hasTable('envato_purchases')) {
            Schema::create('envato_purchases', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username');
                $table->string('purchase_code');
                $table->dateTime('sold_at');
                $table->dateTime('supported_until');
                $table->string('licence');
                $table->integer('ticket_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('envato')) {
            Schema::create('envato', function(Blueprint $table) {
                $table->increments('id');
                $table->string('token');
                $table->string('client_id');
                $table->integer('mandatory');
                $table->timestamps();
            });
        }
        if (!Schema::hasColumn('envato', 'client_id')) {
            Schema::table('envato', function (Blueprint $table) {
                $table->string('client_id');
            });
        }
        if (!Schema::hasColumn('envato', 'allow_expired')) {
            Schema::table('envato', function (Blueprint $table) {
                $table->integer('allow_expired');
            });
        }
        if (!Schema::hasColumn('envato', 'access_token')) {
            Schema::table('envato', function (Blueprint $table) {
                $table->string('access_token');
            });
            Schema::table('envato', function (Blueprint $table) {
                $table->string('refresh_token');
            });
            Schema::table('envato', function (Blueprint $table) {
                $table->longText('envato_account');
            });
        }
        if (!Schema::hasColumn('envato_purchases', 'ticket_id')) {
            Schema::table('envato_purchases', function (Blueprint $table) {
                $table->integer('ticket_id')->nullable();
            });
        }
    }
    public function updatePurchase($event)
    {
        $ticket        = checkArray('ticket', $event);
        $form_data     = checkArray('form_data', $event);
        $purchase_code = checkArray('purchase_code', $form_data);
        if ($ticket && $purchase_code) {
            $purchased = EnvatoPurchase::where('purchase_code', $purchase_code)
                    ->where('ticket_id', 0)
                    ->first();
            if ($purchased) {
                $purchased->ticket_id = $ticket->id;
                $purchased->save();
            }
        }
    }
}
