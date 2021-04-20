<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\TemplateSetRequest;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use Illuminate\Http\Request;
use Lang;
use Exception;
use App\Model\helpdesk\Agent\Department;

class TemplateSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles');
        $tempcon = new TemplateController();
        $this->tempcon = $tempcon;
        
    }

    /**
     * get the list of template sets.
     *
     * @return type view
     */
    public function index()
    {
        try {
            $sets = TemplateSet::all();
            $departments = Department::pluck('name', 'id')->toArray();
            return view('themes.default1.common.template.sets', compact('sets', 'departments'));
        } catch (\Exception $ex) {
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateSet $sets, TemplateSetRequest $request)
    {   
        try {
            // department_id is storing 0 for template set without department and request is returning empty string
            $department_id = empty($request->department_id) ? null : $request->department_id;
            $templates = $sets->where([['template_language', $request->template_language], [
                'department_id', $department_id
                ]])->count();
            $sets->name = $request->input('name');
            $sets->department_id = $department_id;
            $sets->template_language = $request->input('template_language');
            if ($templates == 0) {
                $sets->active = 1;
            }
            $sets->save();
            $templates = Template::where('set_id', '=', '1')->get();
            foreach ($templates as $template) {
                \DB::table('templates')->insert(['set_id' => $sets->id, 'name' => $template->name, 'variable' => $template->variable, 'type' => $template->type, 'subject' => $template->subject, 'message' => $template->message, 'template_category' => $template->template_category]);
            }
            return redirect('template-sets')->with('success', Lang::get('lang.template_saved_successfully'));
        } catch (Exception $ex) {
            return redirect('template-sets')->with('fails', $ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $name
     *
     * @return \Illuminate\Http\Response
     */
    public function activateSet($name)
    {   
        try {
            $templateSets = new TemplateSet();
            $template = $templateSets->where('name', $name)->first();
            if ($template->active == 1) {
                $template->update(['active' => 0]);
                $message = Lang::get('lang.you_have_successfully_deactivated_this_set');
                if ($templateSets->where('active', 1)->count() == 0) {
                    $message = Lang::get('lang.you_have_successfully_deactivated_this_set_made_system_default_active');
                    TemplateSet::whereId(1)->update(['active' => 1]);
                }
            } else {
                $templateSets->where('id', $template->id)->update(['active' => 1]);
                $templateSets->where([['id', '<>', $template->id], ['template_language', $template->template_language], ['department_id', $template->department_id]])->update(['active' => 0]);
                $message = Lang::get('lang.you_have_successfully_activated_this_set');
            }
            return \Redirect::back()->with('success', $message);
        } catch (Exception $ex) {
            return \Redirect::back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->tempcon->showTemplate($id);
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deleteSet($id)
    {
        try {
            if ($id != 1) {
                $templatesSet = TemplateSet::whereId($id)->where('active', 0)->first();
                if ($templatesSet) {
                    Template::where('set_id', $id)->delete();
                    TemplateSet::whereId($id)->delete();
                    return redirect()->back()->with('success', Lang::get('lang.template_set_deleted_successfully'));
                }
            }
            return redirect()->route('template-sets.index')->with('fails', Lang::get('lang.template-set-deletion-error'));
        } catch (Exception $ex) {
            return redirect()->route('template-sets.index')->with('fails', $ex->getMessage());
        }
    }
}
