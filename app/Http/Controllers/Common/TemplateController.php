<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\TemplateRequest;
use App\Http\Requests\helpdesk\TemplateUdate;
use App\Http\Controllers\Common\TemplateVariablesController;
use App\Model\Common\Template;
use App\Model\Common\TemplateType;
use Illuminate\Http\Request;
use Lang;

/**
 * |======================================================
 * | Class Template Controller
 * |======================================================
 * This controller is for CRUD email templates.
 */
class TemplateController extends Controller
{
    public $template;
    public $type;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles');

        $template = new Template();
        $this->template = $template;

        $type = new TemplateType();
        $this->type = $type;
    }

    /**
     * get the list of templates.
     *
     * @return type view
     */
    public function index()
    {
        try {
            return view('themes.default1.common.template.inbox');
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Show template
     * This template to show a particular template.
     *
     * @param type $id
     *
     * @return type view
     */
    public function showTemplate($id)
    {
        try {
            $templates = Template::where('set_id', '=', $id)->count();
            if ($templates > 0) {
                
                return view('themes.default1.common.template.list-templates', compact('id'));
            }
            return redirect()->route('template-sets.index')->with('fails', Lang::get('lang.template-set-not-found'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This function is used to display chumper datatables of the template list.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return type datatable
     */
    public function GetTemplates(Request $request)
    {
        $id = $request->input('id');

        return \Datatable::collection($this->template->where('set_id', '=', $id)->select('id', 'name', 'type')->get())
                        ->showColumns('name')
                        ->addColumn('type', function ($model) {
                            return $this->type->where('id', $model->type)->first()->name;
                        })
                        ->addColumn('action', function ($model) {
                            return '<a href='.url('templates/'.$model->id.'/edit')." class='btn btn-sm btn-primary'>Edit</a>";
                        })
                        ->searchColumns('name')
                        ->orderColumns('name')
                        ->make();
    }

    /**
     * @return type view
     */
    public function create()
    {
        try {
            $i = $this->template->orderBy('created_at', 'desc')->first()->id + 1;
            $type = $this->type->pluck('name', 'id')->toArray();

            return view('themes.default1.common.template.create', compact('type'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * To store a set of templates.
     *
     * @param \App\Http\Requests\helpdesk\TemplateRequest $request
     *
     * @return type redirect
     */
    public function store(TemplateRequest $request)
    {
        try {
            $this->template->fill($request->input())->save();

            return redirect('templates')->with('success', Lang::get('lang.template_saved_successfully'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * function to get the edit page of template.
     *
     * @param type $id
     *
     * @return type
     */
    public function edit($id)
    {
        try {
            $i                  = $this->template->orderBy('created_at', 'desc')->first()->id + 1;
            $template           = $this->template->where('id', $id)->first();
            $type               = $this->type->select('name', 'plugin_name')->where('id', '=', $template->type)->first();
            $body               = $template->message;
            $subject            = $template->subject;
            $templateVarHandler = new TemplateVariablesController();
            $var                = $templateVarHandler->getAvailableTemplateVariables($type->name);
            return view('themes.default1.common.template.edit', compact('template', 'body', 'var', 'type', 'subject'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * function to update a template.
     *
     * @param type                                      $id
     * @param \App\Http\Requests\helpdesk\TemplateUdate $request
     *
     * @return type
     */
    public function update($id, TemplateUdate $request)
    {
        try {
            if (!array_key_exists('variable', $request->all())) {
                $request->merge(['variable' => 0]);
            } else {
                $request->merge(['variable' => 1]);
            }
            $body = $request->get('message');
            $subject = $request->get('subject');
            $request->merge(['message' => $body, 'subject' => $subject]);
            $template = $this->template->where('id', $id)->first();
            $template->fill($request->input())->save();

            return redirect()->back()->with('success', Lang::get('lang.template_updated_successfully'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * function to delete a template.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('select');
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    $template = $this->template->where('id', $id)->first();
                    if ($template) {
                        $template->delete();
                    } else {
                        echo "<div class='alert alert-danger alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>".\Lang::get('message.alert').'!</b>
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        '.\Lang::get('message.no-record').'
                </div>';
                    }
                }
                echo "<div class='alert alert-success alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        ".\Lang::get('message.deleted-successfully').'
                </div>';
            } else {
                echo "<div class='alert alert-danger alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>".\Lang::get('message.alert').'!</b> 
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        '.\Lang::get('message.select-a-row').'
                </div>';
            }
        } catch (\Exception $e) {
            echo "<div class='alert alert-danger alert-dismissable'>
                    <i class='fa fa-ban'></i>
                    <b>".\Lang::get('message.alert').'!</b>
                    <button type=button class=close data-dismiss=alert aria-hidden=true>&times;</button>
                        '.$e->getMessage().'
                </div>';
        }
    }

    /**
     * function to show the templates.
     *
     * @param type $id
     *
     * @return type Mixed
     */
    public function show($id)
    {
        //dd($currency);
        try {
            if ($this->template->where('type', 3)->where('id', $id)->first()) {
                $data = $this->template->where('type', 3)->where('id', $id)->first()->data;
                $products = $this->product->where('id', '!=', 1)->take(4)->get();
                if (count($products) > 0) {
                    $template = '';
                    foreach ($products as $product) {
                        //dd($this->checkPriceWithTaxClass($product->id, $currency));
                        $url = $product->shoping_cart_link;
                        $title = $product->name;
                        if ($product->description) {
                            $description = str_replace('</ul>', '', str_replace('<ul>', '', $product->description));
                        } else {
                            $description = '';
                        }
                        $currency = \Session::get('currency');
                        if ($this->price->where('product_id', $product->id)->where('currency', $currency)->first()) {
                            $product_currency = $this->price->where('product_id', $product->id)->where('currency', $currency)->first();
                            $code = $product_currency->currency;
                            $currency = $this->currency->where('code', $code)->first();
                            if ($currency->symbol) {
                                $currency = $currency->symbol;
                            } else {
                                $currency = $currency->code;
                            }
                            $price = \App\Http\Controllers\Front\CartController::calculateTax($product->id, $product_currency->currency, 1, 0, 1);

                            $subscription = $this->plan->where('id', $product_currency->subscription)->first()->name;
                        } else {
                            return redirect('/')->with('fails', \Lang::get('message.no-such-currency-in-system'));
                        }

                        $array1 = ['{{title}}', '{{currency}}', '{{price}}', '{{subscription}}', '<li>{{feature}}</li>', '{{url}}'];
                        $array2 = [$title, $currency, $price, $subscription, $description, $url];
                        $template .= str_replace($array1, $array2, $data);
                    }

                    //dd($template);
                    return view('themes.default1.common.template.shoppingcart', compact('template'));
                } else {
                    $template = '<p>No Products</p>';

                    return view('themes.default1.common.template.shoppingcart', compact('template'));
                }
            } else {
                return redirect('/')->with('fails', 'no such record');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('fails', $e->getMessage());
        }
    }

    /**
     *
     *
     *
     *
     */
    public function getTemplateTableData($id)
    {
        $temp = $id;
        if ($id == 'settings') {
            $id = 1;
        }
        $calendar_plugin_templates = $this->getCalendarPluginTemplates();
        $templates = Template::join('template_types as ty', 'ty.id', '=', 'templates.type')
            ->select('ty.name as name1', 'templates.name as name2', 'templates.id', 'templates.template_category', 'ty.plugin_name')
            ->where('set_id', '=', $id)->distinct('ty.name')->where(function ($query) {
            $query->whereNull('ty.plugin_name')->orWhere('ty.plugin_name', 'Calendar');
            //Event fire for updating template list
            \Event::dispatch('update_template_list_query_builder', [$query]);
        });

        if(!isPlugin('Calendar')){

            $templates = $templates->whereNotIn('ty.name',  $calendar_plugin_templates);
        } elseif(isPlugin('Calendar') && $temp == 'settings') {
//            $templates = $templates->whereIn('ty.name', $calendar_plugin_templates );
        }
        return \DataTables::of($templates)
                ->editColumn('name1', function ($template) {
                    return $template->name1;
                })
                ->editColumn('name2', function ($template) {
                    return ($template->plugin_name) ? trans($template->plugin_name.'::lang.'.$template->name2): trans('lang.'.$template->name2);
                })
                ->editColumn('id', function ($template) {
                    return '<div class="btn-group" style="width:70px">
                        <a href="'.route('templates.edit', $template->id).'" class="btn btn-primary btn-xs"><i class="fas fa-edit">&nbsp;</i>'.Lang::get('lang.edit').'</a>&nbsp;
                         </div>';
                })
                ->editColumn('template_category', function ($template) {
                    return Lang::get('lang.'.$template->template_category);
                })
                 ->rawColumns(['name1','name2','id'])
                ->make();
    }

    public function getCalendarPluginTemplates()
    {
        $calendar_plugin_templates = [];
        $task_templates = \DB::table('template_types')->select('name')->where('name', 'LIKE', '%task%')->get();
        if(count($task_templates) > 0) {
            foreach ($task_templates as $task_template) {
                array_push($calendar_plugin_templates, $task_template->name);
            }
        }
        return $calendar_plugin_templates;    
    }
}
