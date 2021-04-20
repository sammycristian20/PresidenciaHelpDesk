<?php

namespace App\Http\Controllers\Agent\kb;

// Controllers
use App\Http\Controllers\Controller;
// Request
use App\Http\Requests\kb\ProfilePassword;
use App\Http\Requests\kb\ProfileRequest;
use App\Http\Requests\kb\SettingsRequests;
use App\Model\helpdesk\Settings\System;
// Model
use App\Model\helpdesk\Utility\Timezones;
use App\Model\kb\Comment;
use App\Model\kb\Settings;
// Classes
use App\User;
use Auth;
use Config;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Input;
use Lang;

/**
 * SettingsController
 * This controller is used to perform settings in the setting page of knowledgebase.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class SettingsController extends Controller
{

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */
    protected $ticket_policy;

    public function __construct()
    {
        // checking authentication
        $this->middleware('auth', ['except' => ['settingsGetValue']]);
        $this->language();
    }

    /**
     * to get the settings page.
     *
     * @return response
     */
    public function settings()
    {
        
        /* get the setting where the id == 1 */

        return view('themes.default1.agent.kb.settings.settings');
    }
    /**
     * this methode return all kb settings value with timezone
     *
     * @return response json
     */

    public function settingsGetValue()
    {
        $settings = Settings::whereId('1')->first();
        $timeZone = (!Auth::guest() && Auth::user()) ? Timezones::where('id', Auth::user()->agent_tzone)->value('name') : System::where('id', 1)->value('time_zone');

        return successResponse('', ['kbsettings' => $settings, 'timezone' => $timeZone, 'timeformat' => System::where('id', 1)->value('date_time_format')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function postSettings(SettingsRequests $request)
    {
        try {

            Settings::where('id', 1)->update(['pagination' => $request->pagination, 'status' => $request->status, 'is_comment_enabled' => $request->is_comment_enabled]);
            /* redirect to Index page with Success Message */
            return successResponse('', Lang::get('lang.settings_saved_successfully'));

        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect()->back()->with('fails', Lang::get('lang.settings_can_not_updated'));
        }
    }

    /**
     * Show the Inbox ticket list page.
     *
     * @return type response
     */

    public function kbstatusIndex(Request $request)
    {
        try {
            $kbStatus = $request->input('kb_status');
            Settings::where('id', '1')->update(['status' => $kbStatus]);

            return Lang::get('lang.your_status_updated_successfully');
        } catch (Exception $e) {
            return Redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * To Moderate the commenting.
     *
     * @param type Comment $comment
     *
     * @return Response
     */
    public function comment()
    {
        
        return view('themes.default1.agent.kb.settings.comment');
    }

    /**
     * This method return list of comments based on article id or without article id
     *
     * @return type json
     */
    public function getData(Request $request)
    {
        $articleId = $request->input('article_id');

        $pagination = ($request->input('pagination')) ? $request->input('pagination') : 10;
        $sortBy     = ($request->input('sort-by')) ? $request->input('sort-by') : 'id';
        $search     = $request->input('search-option');
        $filter     = ($request->input('filter_by') || $request->input('filter_by') == "0") ? $request->input('filter_by') : 3;

        $baseQuery = Comment::with('article:kb_article.id,name,slug')->select('id', 'name', 'email', 'website', 'comment', 'status', 'created_at', 'article_id', 'profile_pic')->orderBy($sortBy, 'desc');
        //prepare where condition for array
        $whereConditionForAll      = [];
        $whereConditionForApproved = [['status', 1]];
        $whereConditionForPending  = [['status', 0]];

        if ($articleId) {
            $whereConditionForAll[]      = ['article_id', $articleId];
            $whereConditionForApproved[] = ['article_id', $articleId];
            $whereConditionForPending[]  = ['article_id', $articleId];
            $baseQuery                   = $baseQuery->where('article_id', $articleId);
        }

        $all      = Comment::where($whereConditionForAll)->count();
        $approved = Comment::where($whereConditionForApproved)->count();
        $pending  = Comment::where($whereConditionForPending)->count();

        if ($filter < 2) {
            $baseQuery = $baseQuery->where('status', $filter);
        }
        $searchQuery = $baseQuery->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', '%' . $search . '%')->orWhere('comment', 'LIKE', '%' . $search . '%');
        })->paginate($pagination);
        $count = ['all' => $all, 'approved' => $approved, 'pending' => $pending];
        return successResponse($searchQuery, $count);
    }

    /**
     * Admin can publish the comment.
     *
     * @param type         $commentId of kb_comment
     * @param type Comment $comment
     *
     * @return bool
     */
    public function publish($commentId)
    {
        
        $comment         = Comment::whereId($commentId)->first();
        $comment->status = 1;
        if ($comment->save()) {
            return successResponse(Lang::get('lang.comment_approved'));
        } else {
            return redirect('comment')->with('fails', Lang::get('lang.can_not_process'));
        }
    }

    /**
     * Admin can unapprove the comment.
     *
     * @param type         $commentId of kb_comment
     * @param type Comment $comment
     *
     * @return bool
     */
    public function unapprove($commentId)
    {
        
        $comment         = Comment::whereId($commentId)->first();
        $comment->status = 0;
        if ($comment->save()) {
            return successResponse(Lang::get('lang.comment_unapproved'));
        } else {
            return redirect('comment')->with('fails', Lang::get('lang.can_not_process'));
        }
    }

    /**
     * delete the comment.
     *
     * @param type         $commentId
     * @param type Comment $comment
     *
     * @return type
     */
    public function delete($commentId, Comment $comment)
    {
       
        $comment = $comment->whereId($commentId)->first();
        if ($comment->delete()) {
            return successResponse(Lang::get('lang.comment_deleted'));
        } else {
            return redirect('comment')->with('fails', Lang::get('lang.can_not_process'));
        }
    }

    /**
     * get profile page.
     *
     * @return type view
     */
    public function getProfile()
    {
        $time = Timezone::all();
        $user = Auth::user();

        return view('themes.default1.agent.kb.settings.profile', compact('user', 'time'));
    }

    /**
     * @deprecated
     * Post profile page.
     *
     * @param type ProfileRequest $request
     *
     * @return type redirect
     */
    public function postProfile(ProfileRequest $request)
    {
        $user         = Auth::user();
        $user->gender = $request->input('gender');
        $user->save();
        if (is_null($user->profile_pic)) {
            if ($request->input('gender') == 1) {
                $name              = 'avatar5.png';
                $destinationPath   = 'lb-faveo/dist/img';
                $user->profile_pic = $name;
            } elseif ($request->input('gender') == 0) {
                $name              = 'avatar2.png';
                $destinationPath   = 'lb-faveo/dist/img';
                $user->profile_pic = $name;
            }
        }
        if (Input::file('profile_pic')) {
            //$extension = Input::file('profile_pic')->getClientOriginalExtension();
            $name            = Input::file('profile_pic')->getClientOriginalName();
            $destinationPath = 'lb-faveo/dist/img';
            $fileName        = rand(0000, 9999) . '.' . $name;
            //echo $fileName;
            Input::file('profile_pic')->move($destinationPath, $fileName);
            $user->profile_pic = $fileName;
        } else {
            $user->fill($request->except('profile_pic', 'gender'))->save();

            return redirect()->back()->with('success1', 'Profile Updated sucessfully');
        }
        if ($user->fill($request->except('profile_pic'))->save()) {
            return redirect('profile')->with('success1', 'Profile Updated sucessfully');
        } else {
            return redirect('profile')->with('fails1', 'Profile Not Updated sucessfully');
        }
    }

    /**
     * post profile password.
     *
     * @param type ProfilePassword $request
     *
     * @return type redirect
     */
    public function postProfilePassword(ProfilePassword $request)
    {

        $user = Auth::user();

        if (Hash::check($request->input('old_password'), $user->getAuthPassword())) {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            return redirect('profile')->with('success2', 'Password Updated sucessfully');
        } else {
            return redirect('profile')->with('fails2', 'Old password Wrong');
        }
    }

    /**
     * het locale for language.
     *
     * @return type config set
     */
    public static function language()
    {
        // $set = Settings::whereId(1)->first();
        // $lang = $set->language;
        Config::set('app.locale', 'en');
        Config::get('app');
    }

}
