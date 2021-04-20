<?php

namespace App\Http\Controllers\Admin\helpdesk\Source;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Manage\Tickettype;
use Lang;
use App\Model\helpdesk\Ticket\Ticket_source;
use App\Location\Models\Location;
use DB;

class SourceController extends Controller {
    public function __construct() {
        $this->middleware(['auth', 'roles'])->except('findLocation');
    }

    /**
     * 
     * @return type view
     */
    public function source() {
        return view('themes.default1.admin.helpdesk.source.index');
    }

    /**
     * 
     * @return type json
     */
    public function ajaxDatatable() {
        $source = Ticket_source::all();

        return \DataTables::of($source)
                        ->editColumn(
                                'name', function ($source) {
                            $name = str_limit($source->name, 15);
                            return $name;
                        }
                        )
                        ->editColumn(
                                'value', function ($source) {
                            $value = str_limit($source->value, 15);
                            return $value;
                        }
                        )
                        ->editColumn(
                                'description', function ($source) {
                            $comment = str_limit($source->description, 100);
                            $title = strip_tags($source->description);

                            $view = "<ul class='nav nav-stacked'><span style='word-wrap: break-word;' title='$title'>$comment</span></ul>";
                            return $view;
                        }
                        )
                        ->addColumn('action', function($source) {
                            if ($source->is_default == 1) {
                                return "<a href=" . url('source/' . $source->id . '/edit') . " class='btn btn-primary btn-xs' ><i class='fas fa-edit' style='color:white;'></i>&nbsp;Edit</a>&nbsp;&nbsp;<button class='btn btn-primary btn-xs' disabled='disabled' ><i class='fas fa-trash' style='color:white;'></i>&nbsp;Delete </button>";
                            } else {

                                $url = url('source/' . $source->id . '/delete');
                                $confirmation = deletePopUp($source->id, $url, "Delete", 'btn btn-primary btn-xs');

                                return "<a href=" . url('source/' . $source->id . '/edit') . " class='btn btn-primary btn-xs'><i class='fas fa-edit' style='color:white;'></i>&nbsp;Edit</a>&nbsp;&nbsp;"
                                        . $confirmation;
                            }
                        })
                        ->rawColumns(['action', 'description'])
                        ->make();
    }

    /**
     * 
     * @return type view
     */
    public function create() {
        try {
            $location = Location::all();
            return view('themes.default1.admin.helpdesk.source.create', compact('location'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type view
     */
    public function edit($id) {
        try {
            $source = Ticket_source::find($id);
            if (!$source) {
                return redirect()->to(404)->with('fails', trans('lang.not_found'));
            }
            $location = Location::all();
            //unused code should be removed
            $source_location = " ";
            if ($source->location) {
                $location_id = Location::where('title', '=', $source->location)->select('id')->first();
                $source_location = $location_id->id;
            }
            return view('themes.default1.admin.helpdesk.source.edit', compact('source', 'location', 'source_location'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @param \Illuminate\Http\Request $request
     * @return type Response
     */
    public function update($id, Request $request) {
        $this->validate(
                $request, [
            'source' => 'max:20|required|unique:ticket_source,name,' . $id,
            'display_as' => 'max:20|required|unique:ticket_source,value,' . $id,
                ]
        );
        try {
            // dd(implode(",", $request->input('location')));

            $source = Ticket_source::find($id);
            //        if($request->input('location')){
            //              $location_id = implode(",", $request->input('location'));
            //              $location_name=Location::where('id','=',$location_id)->select('title')->first();
            //              $location=$location_name->title;
            //         }
            //         else{
            //              $location ="";
            //         }

            if ($source) {
                $source->update(
                        [
                            'name' => $request->input('source'),
                            'value' => $request->input('display_as'),
                            // 'location' =>$location,
                            'description' => $request->input('description'),
                            // 'css_class' => $request->input('icon_class')
                        ]
                );
            } else {
                return redirect()->back()->with('fails', 'Source not found');
            }
            return redirect('source')->with('success', Lang::get('lang.source_updated_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param \Illuminate\Http\Request $request
     * @return type Response
     */
    public function postCreate(Request $request) {
        $this->validate(
                $request, [
            'source' => 'required|unique:ticket_source,name|max:20',
            'display_as' => 'required|unique:ticket_source,value|max:20',
                // 'icon_class' =>'required|unique:ticket_source,css_class',
                // 'description' => 'max:100'
                ]
        );
        try {
            // if($request->input('location')){
            //      $location_id = implode(",", $request->input('location'));
            //      $location_name=Location::where('id','=',$location_id)->select('title')->first();
            //      $location=$location_name->title;
            // }
            // else{
            //      $location ="";
            // }
            Ticket_source::create(
                    [
                        'name' => $request->input('source'),
                        'value' => $request->input('display_as'),
                        // 'location' =>$location,
                        'description' => $request->input('description'),
                        // 'css_class' => $request->input('icon_class')
                    ]
            );
            return redirect('source')->with('success', Lang::get('lang.source_saved_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * 
     * @param type $id
     * @return type Response
     */
    public function destroy($id) {
        try {

            $querys = DB::table('sla_plan')
                            ->whereRaw('FIND_IN_SET(?,apply_sla_ticketsource)', [$id])
                            ->pluck('id')->toArray();
            if ($querys) {
                return redirect()->back()->with('fails', Lang::get('lang.you_cannot_delete_this_source,source_associated_sla_plan'));
            }

            $source = Ticket_source::find($id);
            $ticket_check_source = Tickets::where('source', '=', $id)->count();

            if ($ticket_check_source > 0) {
                return redirect()->back()->with('fails', Lang::get('lang.you_cannot_delete_this_source,this_source_applied_some_tickets'));
            }

            if ($source) {
                $source->delete();
            } else {
                return redirect()->back()->with('fails', 'Source not found');
            }
            return redirect('source')->with('success', Lang::get('lang.source_deleted_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }



}
