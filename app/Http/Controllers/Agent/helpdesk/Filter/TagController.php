<?php

namespace App\Http\Controllers\Agent\helpdesk\Filter;

use App\Events\Ticket\TicketUpdating;
use App\Model\Common\TicketActivityLog;
use App\Model\helpdesk\Filters\Label;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use App\Model\helpdesk\Filters\Tag;
use App\Model\helpdesk\Filters\Filter;
use App\Model\helpdesk\Manage\Tickettype;
use Lang;
use DB;
use App\Model\kb\KbArticleTag;
use App\Http\Requests\helpdesk\Ticket\TagsCreateFromInboxRequest;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role.agent']);
        $this->middleware('roles', ['only' => ['tag', 'create', 'edit']]);
    }

    public function store(Request $request)
    {
        try {
            $tag = new Tag();
            return $this->save($tag, $request->input());
        } catch (Exception $ex) {
//            dd($ex);
        }
    }

    public function save($model, $request)
    {
        $tags = $request->input('tags');
        if (is_array($tags) && count($tags) > 0) {
            foreach ($tags as $t) {
                $tag = $model->where('name', $t)->first();
                if (!$tag) {
                    if ($t != '') {
                        $model->create([
                            'name' => $t,
                            'description' => '',
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Add Tags here by passing ticket id and the selected tags
     *
     * @date   2019-05-14T16:25:45+0530
     *
     * @param  TagsCreateFromInboxRequest $request
     * @param bool $sync if all tags has to be overwritten. If passed false, it is going to append new labels
     * @return json
     */
    public function addToFilter(TagsCreateFromInboxRequest $request, $sync = true)
    {
        $ticket_id = $request->input('ticket_id');
        $tags = $request->input('tags');
        $tag = new Tag();
        $this->save($tag, $request);

        $this->saveToFilter($ticket_id, $tags, $sync);

        $tagsIds = Tag::whereIn("name", $tags)->pluck("id")->toArray();

        event(new TicketUpdating(["tag_ids"=>$tagsIds]));

        TicketActivityLog::saveActivity($ticket_id);

        return successResponse(Lang::get('lang.tag_successfully_added'));
    }

    public function saveToFilter($ticket_id, $tags, $sync)
    {
        $filters = new \App\Model\helpdesk\Filters\Filter();
        $filter = $filters->where('key', 'tag')->where('ticket_id', $ticket_id)->get();
        if ($filter->count() > 0 && $sync) {
            foreach ($filter as $f) {
                $f->delete();
            }
        }
        if ($tags && is_array($tags)) {
            foreach ($tags as $tag) {
                if ($tag != '') {
                    $filters->updateOrCreate(['ticket_id' => $ticket_id, 'key' => 'tag', 'value' => $tag]);
                }
            }
        }
    }
    /**
     * Search for a tag
     * @param Request $request
     * @return type
     */
    public function getTag(Request $request)
    {
        $term = $request->input('search-query');
        $tag = new Tag();
        $tags = $tag->where('name', 'LIKE', '%' . $term . '%')->pluck('name')->toArray();
        return successResponse('', ['tags' => $tags]);
    }
    
    /**
     * Get tags for a particular ticket
     *
     * @date   2019-05-14T17:26:39+0530
     *
     * @param  int $ticketid
     *
     * @return json
     */
    public function getTagForTicket($ticketid)
    {
        $filter = Filter::where('key', 'tag')->where('ticket_id', $ticketid)->pluck('value')->toArray();
        return successResponse('', ['tags' => $filter]);
    }
    /**
     *
     * @param Request $request
     * @return type
     */
    public function getType(Request $request)
    {
        $term = $request->input('term');

        $types = new Tickettype();

        $type = $types->where('name', 'LIKE', '%' . $term . '%')->where('status', '=', 1)->pluck('name')->toArray();

        return $type;
    }
    /**
     *
     * @return type
     */
    public function tag()
    {
        return view('themes.default1.admin.helpdesk.tag.index');
    }
    /**
     *
     * @return type
     */
    public function ajaxDatatable()
    {
        $tags = Tag::leftJoin('filters', function ($join) {
            $join->on('tags.name', '=', 'filters.value')
                    ->where('filters.key', '=', 'tag')
                    ;
        })
                ->select(
                    'tags.id',
                    'tags.name',
                    'tags.description',
                    \DB::raw('count(filters.ticket_id) as count')
                )
                ->groupBy('tags.name')
        //->get()
        ;
        return \DataTables::of($tags)
                        ->editColumn('count', function ($tag) {
                            return  $tag->count ;
                        })
                        ->addColumn('action', function ($tag) {
                            $delete = deletePopUp($tag->id, url('tag/' . $tag->id . '/delete'), $title = "Delete", $class = "btn btn-primary btn-xs", $btn_name= "Delete", $button_check = true, $methodName = 'delete');
                            $edit = "<a href='" . url('tag/' . $tag->id . '/edit') . "' class='btn btn-primary btn-xs'><i class='fa fa-edit' style='color:white;'>&nbsp;</i>" . trans('lang.edit') . "</a>&nbsp;";
                           
                            return $edit . " " . $delete;
                        })
                        ->filterColumn('count', function ($query, $keyword) {
                            //for making search by name possible
                        })
                        ->removeColumn('id')
                        ->rawColumns(['count','action','description'])
                        ->make();
    }
    /**
     *
     * @return type
     */
    public function create()
    {
        try {
            return view('themes.default1.admin.helpdesk.tag.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     *
     * @param type $id
     * @return type
     */
    public function edit($id)
    {
        try {
            $tag = Tag::find($id);
            if (!$tag) {
                return redirect()->back()->with('fails', 'Tag not found');
            }
            return view('themes.default1.admin.helpdesk.tag.edit', compact('tag'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     *
     * @param type $id
     * @param Request $request
     * @return type
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'max:20|required|unique:tags,name,' . $id,
             'description' => 'max:50'
        ]);
        try {
            $tag = Tag::find($id);
            if ($tag) {
                $tag->update([
                    'name' => $request->input('name'),
                    'description' => $request->input('description')
                ]);
            } else {
                return redirect()->back()->with('fails', 'Tag not found');
            }
            return redirect('tag')->with('success', Lang::get('lang.tag_updated_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     *
     * @param Request $request
     * @return type
     */
    public function postCreate(Request $request)
    {
        //both blade file and view file are using same method 
         if($request->input('kb'))
            {

                $checkCount = Tag::where('name',$request->input('name'))->count();
                if($checkCount){
                    return errorResponse(Lang::get('lang.The_name_has_already_been_taken'));
                }
                Tag::create(['name' => $request->input('name')]);
            
              return successResponse(Lang::get('lang.tag_saved_successfully'));
            }
            
            $this->validate($request, [
            'name' => 'required|unique:tags,name|max:20',
            'description' => 'max:50'
             ]);
            
        
        try {
            Tag::create([
                'name' => $request->input('name'),
                'description' => $request->input('description')
            ]);

           return redirect('tag')->with('success', Lang::get('lang.tag_saved_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     *
     * @param type $id
     * @return type
     */
    public function destroy($id)
    {
        try {
            $tag = Tag::find($id);
            $querys = DB::table('sla_plan')
                            ->whereRaw('FIND_IN_SET(?,apply_sla_tags)', [$tag->name])
                            ->pluck('id')->toArray();
            if ($querys) {
                return redirect()->back()->with('fails', Lang::get('lang.you_cannot_delete_this_tag,this_tag_applied_sla_plan'));
            }

            $query = KbArticleTag::where('tag_id',$id)->count();
            if($query)
            {
              return redirect()->back()->with('fails', Lang::get('lang.you_can_not_delete_this_tag_as_some_articles_are_linked_with_this_tag'));   
            }


            if ($tag) {
                $tag->delete();
            } else {
                return redirect()->back()->with('fails', 'Tag not found');
            }
            return redirect('tag')->with('success', Lang::get('lang.tag_deleted_successfully'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
}
