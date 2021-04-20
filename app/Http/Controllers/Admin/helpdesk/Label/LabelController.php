<?php

namespace App\Http\Controllers\Admin\helpdesk\Label;

use App\Events\Ticket\TicketUpdating;
use App\Model\Common\TicketActivityLog;
use Illuminate\Http\Request;
use App\Http\Requests\helpdesk\LableUpdate;
use App\Http\Controllers\Controller;
use Exception;
use App\Model\helpdesk\Filters\Label;
use Datatable;
use Lang;
use DB;
use App\Constants\Permission;

class LabelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role.agent']);
        $this->middleware('roles', ['only' =>['index', 'create', 'edit']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('themes.default1.admin.helpdesk.label.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('themes.default1.admin.helpdesk.label.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:labels|max:50',
            'color' => 'required|regex:/#([a-fA-F0-9]{3}){1,2}\b/',
            'order' => 'required|unique:labels|integer',
        ]);
        try {
            $model = new Label();
            $result = $this->save($model, $request->input());
            if ($result) {
                return redirect('labels')->with('success', Lang::get('lang.labels_saved_successfully'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $labels = new Label();
            $label = $labels->find($id);
            if (!$label) {
                throw new Exception('Sorry! We are not able to find your request');
            }
            return view('themes.default1.admin.helpdesk.label.edit', compact('label'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LableUpdate $request, $id)
    {
        try {
            $labels = new Label();
            $label = $labels->find($id);
            if (!$label) {
                throw new Exception('Sorry! We are not able to find your request');
            }
            $result = $this->save($label, $request->input());
            if ($result) {
                return redirect('labels')->with('success', Lang::get('lang.labels_updated_successfully'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $label = Label::find($id);
            $querys = DB::table('sla_plan')
                            ->whereRaw('FIND_IN_SET(?,apply_sla_labels)', [$label->title])
                            ->pluck('id')->toArray();
            if ($querys) {
                return redirect()->back()->with('fails', Lang::get('lang.you_cannot_delete_this_label,this_label_applied_sla_plan'));
            }
            Label::where('id', '=', $id)->first()->delete();
            return redirect()->back()->with('success', Lang::get('lang.labels_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }
    /**
     *
     * @param type $model
     * @param type $request
     * @param type $json
     * @return type json
     */
    public function save($model, $request, $json = false)
    {
        try {
            $result = $model->fill($request)->save();
            if ($json == true) {
                $result = $model->get()->toJson();
            }
            return $result;
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function ajaxTable()
    {
        $label = new Label();
        $labels = $label->select('id', 'title', 'color', 'order', 'status')->get();
        return \DataTables::of($labels)
                        ->addColumn('title', function ($model) {
                            return $model->titleWithColor();
                        })
                        ->addColumn('status', function ($model) {
                            return $model->status();
                        })
                        ->addColumn('action', function ($model) {
                            $url = url('labels/delete/' . $model->id);
                            $confirmation = deletePopUp($model->id, $url, "Delete $model->subject");
                            $edit = "<a href='" . url('labels/' . $model->id . '/edit') . "' class='btn btn-xs btn-primary'><i class='fa fa-edit' style='color:white;'>&nbsp;</i>Edit</a>&nbsp;&nbsp; $confirmation";
                            return "<span>" . $edit . "</span>";
                        })
                        ->rawColumns(['title', 'action'])
                        ->make();
    }


    /**
     * Send ticket id and the selected labels here for saving the labels for a ticket
     * @param Request $request
     * @param bool $sync if all labels has to be overwritten. If passed false, it is going to append new labels
     * @return json
     */
    public function attachTicket(Request $request, $sync = true)
    {
        $filters = new \App\Model\helpdesk\Filters\Filter();
        $ticketid = $request->input('ticket_id');
        $labels = $request->input('labels');
        $filter = $filters->where('ticket_id', $ticketid)->where('key', 'label')->get();

        if ($filter->count() > 0 && $sync) {
            foreach ($filter as $f) {
                $f->delete();
            }
        }

        if (count($labels) > 0) {

            foreach ($labels as $label) {
                $filters->updateOrCreate(['ticket_id' => $ticketid, 'key' => 'label', 'value' => $label]);
            }
        }

        $labelIds = Label::whereIn("title", $labels)->pluck("id")->toArray();

        event(new TicketUpdating(["label_ids"=>$labelIds]));

        TicketActivityLog::saveActivity($ticketid);

        return successResponse(Lang::get('lang.label_successfully_added'));
    }
    /**
     *
     * @param Request $request
     * @return type
     */
    public function getLabel(Request $request)
    {
        $term = $request->input('term');
        $response = $this->labels($term, 'array');
        return response($response, 200);
    }
    /**
     *
     * @param type $search
     * @param type $type
     * @return type
     */
    public function labels($search = "", $type = 'collection')
    {
        $labels = new Label();
        if ($search == "") {
            $output = $labels->select('title');
        } else {
            $output = $labels->select('title')->where('title', 'LIKE', '%' . $search . '%');
        }
        switch ($type) {
            case "array":
                return $output->pluck('title')->toArray();
            case "json":
                return $output->get()->toJson();
            default:
                return $output->get();
        }
    }
}
