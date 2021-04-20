<?php
namespace App\Bill\database\seeds\v2_0_0;

use App\Bill\Models\PaymentGateway;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateType;
use App\Model\Common\TemplateShortCode;
use App\Model\helpdesk\Settings\CommonSettings;

use database\seeds\v_1_9_50\DatabaseSeeder as ParentSeeder;

class DatabaseSeeder extends ParentSeeder
{
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reports Settings Seeder
        $this->reportsSettingsSeeder();
        $this->updateTemplate();
        $this->seedTemplateShortCodes();
        CommonSettings::updateOrCreate([
            'option_name'    => 'bill',
            'optional_field' => 'version'
        ],[
            'option_name'    => 'bill',
            'optional_field' => 'version',
            'option_value'   => 'v2.0.0'
        ]);
    }

    private function updateTemplate()
    {
        $sets  = TemplateSet::all()->pluck('id')->toArray();
        $this->createSendInviceTemplate($sets);
        $this->createPurchaseConfirmationTemplate($sets);
        $this->createPaymentArrovalTemplate($sets);
        
    }

    private function createSendInviceTemplate($sets)
    {
        $type  = TemplateType::updateOrCreate(['name' => 'send-invoice'], ['name' => 'send-invoice', 'plugin_name' => 'Bill'])->id;
        // if($this->checkTemplateExists($type, 'common-tmeplates') == 0)
        // {
            $name = 'template-send-invoice';
            $message = '<p>Hello {!! $receiver_name !!}<br /><br />'
                        .'We found that you were interested in purchasing one of our support packages. We have raised and attached an invoice for the same with this email.</p>'
                        .'<p>Please continue to checkout process so we can confirm your order.</p>.'
                        .'<p><br />Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
                        .'<p>&nbsp;</p>';
            $this->createTemplateWithGivenCategory($sets, $type, 'common-templates', $message, $name);
        // }
    }

    private function createPurchaseConfirmationTemplate($sets)
    {
        $type  = TemplateType::updateOrCreate(['name' => 'purchase-confirmation'], ['name' => 'purchase-confirmation', 'plugin_name' => 'Bill'])->id;
        // if($this->checkTemplateExists($type, 'agent-templates') == 0)
        // {
            $name = 'template-purchase-notification';
            $message = '<p>Hello {!! $receiver_name !!}</p><br />'
                        .'<p>An order of a user has been confirmed for a support package in the store. Below are the details of the order.</p>'
                        .'<p>Order Details:</p>'
                        .'<p><strong>Invoice No</strong>:&nbsp;{!! $invoice_id !!}</p>'
                        .'<p><strong>Client Name</strong>:&nbsp;{!! $invoice_user_name !!}</p>'
                        .'<p><strong>Amount Paid</strong>:&nbsp;{!! $invioce_amount_paid !!}</p>'
                        .'<p><strong>Paid date:</strong> {!! $invoice_paid_date !!}</p>'
                        .'<p><strong>Payment mode</strong>:&nbsp;{!! $invoice_payment_mode !!}</p>'
                        .'<p><strong>Package Name</strong>:&nbsp;{!! $package_name !!}</p>'
                        .'<p><strong>Package Validity</strong>:&nbsp;{!! $package_validity !!}</p>'
                        .'<p><strong>Package expriry date</strong>: {!! $order_expriy_date !!}</p>'
                        .'<p><strong>Package Credit Type</strong>: {!! $package_credit_type !!}</p>'
                        .'<p><strong>Last Transaction amount</strong>: {!! $last_transaction_amount !!}</p>'
                        .'<p><strong>Last Transaction processed by</strong>:&nbsp;{!! $last_transaction_by_name !!}</p>'
                        .'<p>Please find the attached invoice for more details about the order.</p>'
                        .'<p><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>';
            $this->createTemplateWithGivenCategory($sets, $type, 'agent-templates', $message, $name);
        // }

        // if($this->checkTemplateExists($type, 'client-templates') == 0)
        // {
            $name = 'template-purchase-confirmation';
            $message = '<p>Hello {!! $receiver_name !!}<br />'
                        .'Your recent purchase has been confirmed. You can use the purchased package for raising support tickets.</p>'
                        .'<p>Order Details:</p>'
                        .'<p><strong>Invoice No</strong>:&nbsp;{!! $invoice_id !!}</p>'
                        .'<p><strong>Amount Paid</strong>:&nbsp;{!! $invioce_amount_paid !!}</p>'
                        .'<p><strong>Paid date:</strong> {!! $invoice_paid_date !!}</p>'
                        .'<p><strong>Package Name</strong>:&nbsp;{!! $package_name !!}</p>'
                        .'<p><strong>Package Validity</strong>:&nbsp;{!! $package_validity !!}</p>'
                        .'<p><strong>Package expriry date</strong>: {!! $order_expriy_date !!}</p>'
                        .'<p><strong>Package Credit Type</strong>: {!! $package_credit_type !!}</p>'
                        .'<p>Please find the attached invoice for your order.<br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>'
                        .'<p>&nbsp;</p>';
            $this->createTemplateWithGivenCategory($sets, $type, 'client-templates', $message, $name);
        // }


    }

    private function createPaymentArrovalTemplate($sets)
    {
        $type  = TemplateType::updateOrCreate(['name' => 'payment-approval'], ['name' => 'payment-approval', 'plugin_name' => 'Bill'])->id;
        // if($this->checkTemplateExists($type, 'agent-templates') == 0)
        // {
            $name = 'template-payment-approval';
            $message = '<p>Hello {!! $receiver_name !!}</p><br />'
                        .'<p>A user has purchased a package and selected on an offline payment&nbsp;method.</p><br />'
                        .'<p>Order Details:</p>'
                        .'<p><strong>Invoice No</strong>:&nbsp;{!! $invoice_id !!}</p>'
                        .'<p><strong>Invoice Link</strong>:&nbsp;{!! $invoice_link_for_agent !!}</p>'
                        .'<p><strong>Client Name</strong>:&nbsp;{!! $invoice_user_name !!}</p>'
                        .'<p><strong>Amount Paid</strong>:&nbsp;{!! $invioce_amount_paid !!}</p>'
                        .'<p><strong>Paid date:</strong> {!! $invoice_paid_date !!}</p>'
                        .'<p><strong>Payment mode</strong>:&nbsp;{!! $invoice_payment_mode !!}</p>'
                        .'<p><strong>Package Name</strong>:&nbsp;{!! $package_name !!}</p>'
                        .'<p><strong>Package Validity</strong>:&nbsp;{!! $package_validity !!}</p>'
                        .'<p><strong>Package expriry date</strong>: {!! $order_expriy_date !!}</p>'
                        .'<p><strong>Package Credit Type</strong>: {!! $package_credit_type !!}</p><br />'
                        .'<p>Please contact the user to&nbsp;verify payment details and confirm the order</p>'
                        .'<p><br />Kind Regards,'
                        .'<br />{!! $system_from !!}</p>';
            $this->createTemplateWithGivenCategory($sets, $type, 'agent-templates', $message, $name);
        // }
    }

    private function reportsSettingsSeeder()
    {
        if(PaymentGateway::count() == 0) {
            foreach ($this->getExtra() as $value) {
                PaymentGateway::create(array_merge($value, ['name' => 'PayPal', 'gateway_name' => 'PayPal_Rest', 'status' => 0, 'is_default' => 0]));
            }
            PaymentGateway::create(['name' => 'Bank Transfer', 'status' => 1, 'is_default' => 1]);
            PaymentGateway::create(['name' => 'Cash', 'status' => 1, 'is_default' => 0]);
        }
    }

    private function getExtra()
    {
        return [
            [
                'key'          => 'clientID',
                'value'        => null
            ],
            [
                'key'          => 'secret',
                'value'        => null
            ],
            [
                'key'          => 'testMode',
                'value'        => 0
            ]
        ];
    }

    private function seedTemplateShortCodes()
    {
        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_id"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_id !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_id_description',
            'key_name'             => 'invoice_id'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_created_at"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_created_at !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_created_at_description',
            'key_name'             => 'invoice_created_at'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_total_amount"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_total_amount !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_total_amount_description',
            'key_name'             => 'invoice_total_amount'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invioce_payable_amount"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invioce_payable_amount !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invioce_payable_amount_description',
            'key_name'             => 'invioce_payable_amount'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_payment_mode"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_payment_mode !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_payment_mode_description',
            'key_name'             => 'invoice_payment_mode'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_due_by"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_due_by !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_due_by_description',
            'key_name'             => 'invoice_due_by'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invioce_amount_paid"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invioce_amount_paid !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invioce_amount_paid_description',
            'key_name'             => 'invioce_amount_paid'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_paid_date"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_paid_date !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_paid_date_description',
            'key_name'             => 'invoice_paid_date'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_user_name"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_user_name !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_user_name_description',
            'key_name'             => 'invoice_user_name'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_user_email"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_user_email !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_user_email_description',
            'key_name'             => 'invoice_user_email'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_link_for_client"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_link_for_client !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_link_for_client_description',
            'key_name'             => 'invoice_link_for_client'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_link_for_agent"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_link_for_agent !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_link_for_agent_description',
            'key_name'             => 'invoice_link_for_agent'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_discount"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_discount !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_discount_description',
            'key_name'             => 'invoice_discount'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "invoice_tax"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $invoice_tax !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_invoice_tax_description',
            'key_name'             => 'invoice_tax'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "package_credit_type"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $package_credit_type !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_package_credit_type_description',
            'key_name'             => 'package_credit_type'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "order_id"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $order_expriy_date !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_order_id_description',
            'key_name'             => 'order_id'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "order_expriy_date"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $order_expriy_date !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_order_expriy_date_description',
            'key_name'             => 'order_expriy_date'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "order_link_for_client"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $order_link_for_client !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_order_link_for_client_description',
            'key_name'             => 'order_link_for_client'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "package_name"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $package_name !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_package_name_description',
            'key_name'             => 'package_name'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "package_description"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $package_description !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_package_description_description',
            'key_name'             => 'package_description'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "package_price"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $package_price !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_package_price_description',
            'key_name'             => 'package_price'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "package_validity"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $package_validity !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_package_validity_description',
            'key_name'             => 'package_validity'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "last_transaction_id"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $last_transaction_id !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_last_transaction_id_description',
            'key_name'             => 'last_transaction_id'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "last_transaction_amount"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $last_transaction_amount !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_last_transaction_amount_description',
            'key_name'             => 'last_transaction_amount'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "last_transaction_status"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $last_transaction_status !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_last_transaction_status_description',
            'key_name'             => 'last_transaction_status'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "last_transaction_method"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $last_transaction_method !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_last_transaction_method_description',
            'key_name'             => 'last_transaction_method'
        ]);

        TemplateShortCode::updateOrCreate([
            'key_name'             => "last_transaction_by_name"
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $last_transaction_by_name !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_last_transaction_by_name_description',
            'key_name'             => 'last_transaction_by_name'
        ]);

        TemplateShortCode::updateOrCreate([
          'key_name'             => "last_transaction_by_email"
            
        ],[
            'plugin_name'          => 'Bill',
            'shortcode'            => '{!! $last_transaction_by_email !!}',           
            'description_lang_key' => 'Bill::lang.shortcode_last_transaction_by_email_description',
            'key_name'             => 'last_transaction_by_email'
        ]); 
    }
}