<?php

namespace App\Http\Controllers\Client\kb;

use App\Http\Controllers\Controller;
use App\Http\Requests\kb\CommentRequest;
use App\Http\Requests\kb\ContactRequest;
use App\Http\Requests\kb\ProfilePassword;
use App\Http\Requests\kb\SearchRequest;
use App\Model\kb\Article;
use App\Model\kb\Category;
use App\Model\kb\Comment;
use App\Model\kb\Contact;
use App\Model\kb\Faq;
use App\Model\kb\Page;
use App\Model\kb\Relationship;
use App\Model\kb\Settings;
use Auth;
// use Creativeorange\Gravatar\Gravatar;
use Config;
use Hash;
use Illuminate\Http\Request;
use Lang;
use Mail;
use Redirect;
use App\User;
use App\Http\Controllers\Common\PhpMailController;
use DB;
use App\Model\helpdesk\Settings\System;
use Carbon\Carbon;

class UserController extends Controller {

      public function __construct(PhpMailController $phpMailController)
    {
        $this->PhpMailController = $phpMailController;
        $this->middleware('board');
        $this->middleware('kbsettings');
    }

   

    /**
     * This method return all article list according user role
     *
     * @return response view
     */
    public function getArticle()
    {
        
         return view('themes.default1.client.kb.article-list.articles');
    }

    /**
     * Get excerpt from string.
     *
     * @param string $str       String to get an excerpt from
     * @param int    $startPos  Position int string to start excerpt from
     * @param int    $maxLength Maximum length the excerpt may be
     *
     * @return string excerpt
     */
    public static function getExcerpt($str, $startPos = 0, $maxLength = 50)
    {
        if (strlen($str) > $maxLength) {
            $excerpt = substr($str, $startPos, $maxLength - 3);
            $lastSpace = strrpos($excerpt, ' ');
            $excerpt = substr($excerpt, 0, $lastSpace);
            $excerpt .= '...';
        } else {
            $excerpt = $str;
        }

        return $excerpt;
    }

    /**
     * function to search an article.
     *
     * @param \App\Http\Requests\kb\SearchRequest $request
     * @param \App\Model\kb\Category              $category
     * @param \App\Model\kb\Article               $article
     * @param \App\Model\kb\Settings              $settings
     *
     * @return type view
     */
    public function search()
    {
         return view('themes.default1.client.kb.article-list.search');
    }

    /**
     * This method displaying selected article  
     * @param type $slug
     * @param Article $article
     * @param Category $category
     * @param Settings $settings
     * @return type view
     */
    public function show($slug, Article $article, Category $category, Settings $settings)
    {

        $loggedInUser = Auth::user();
        $visibleTo = !$loggedInUser ? ['all_users'] : ( $loggedInUser->role == 'user' ? ['logged_in_users', 'all_users'] :
                ( $loggedInUser->role == 'agent' ? ['all_users', 'agents'] : ['all_users', 'logged_in_users', 'agents'] ) );
        $checkArticle=Article::where('slug', $slug)->value('visible_to');
         if(!in_array( $checkArticle,$visibleTo)){
           return redirect('/')->with('fails', Lang::get('lang.you_dont_have_permission_to_open_this_page'));
         }

         return view('themes.default1.client.kb.article-list.show');
        
    }

    /**
     * this method return category view with article
     * @param string $slug
     * @return view     Category view and article view
     */
    public function getCategoryArticles()
    {
       
        /* direct to view with $article_id */
        return view('themes.default1.client.kb.article-list.category');
    }

    /**
     * 
     * @param Article $article
     * @param Category $category
     * @param Relationship $relation
     * @param Settings $settings
     * @return type
     */
    public function home(Article $article, Category $category, Relationship $relation, Settings $settings)
    {
        
            return view('themes.default1.client.kb.article-list.home');
        
    }

    /**
     * 
     * @param Faq $faq
     * @param Category $category
     * @return type
     */
    public function Faq(Faq $faq, Category $category)
    {
        $faq = $faq->where('id', '1')->first();
        $categorys = $category->get();
        return view('themes.default1.client.kb.article-list.faq', compact('categorys', 'faq'));
    }

    /**
     * 
     * @param Category $category
     * @param Settings $settings
     * @return type view
     */
    public function contact(Category $category, Settings $settings)
    {
        $settings = $settings->whereId('1')->first();
        $categorys = $category->get();
        return view('themes.default1.client.kb.article-list.contact', compact('settings', 'categorys'));
    }

    /**
     * send message to the mail adderess that define in the system.
     *
     * @return response
     */
    public function postContact(ContactRequest $request, Contact $contact)
    {


        $this->port();
        $this->host();
        $this->encryption();
        $this->email();
        $this->password();
        //return Config::get('mail');
        $contact->fill($request->input())->save();
        $name = $request->input('name');
        //echo $name;
        $email = $request->input('email');
        //echo $email;
        $subject = $request->input('subject');
        //echo $subject;
        $details = $request->input('message');
        //echo $message;
        //echo $contact->email;
        $mail = Mail::send(
                        'themes.default1.client.kb.article-list.contact-details', ['name' => $name, 'email' => $email, 'subject' => $subject, 'details' => $details], function ($message) use ($contact) {
                    $message->to($contact->email, $contact->name)->subject('Contact');
                }
        );
        if ($mail) {
            return redirect('contact')->with('success', Lang::get('lang.your_details_send_to_system'));
        } else {
            return redirect('contact')->with('fails', Lang::get('lang.your_details_can_not_send_to_system'));
        }
    }

    /**
     * 
     * @return type
     */
    public function contactDetails()
    {
        return view('themes.default1.client.kb.article-list.contact-details');
    }

    /**
     * To insert the values to the comment table.
     *
     * @param type Article $article
     * @param type Request $request
     * @param type Comment $comment
     * @param type Id      $id
     *
     * @return type response
     */
    public function postComment($slug, Article $article, CommentRequest $request, Comment $comment)
    {
        try{
       $article = $article->where('slug', $slug)->first();
        if (!$article) {
            return Redirect::back()->with('fails', Lang::get('lang.sorry_not_processed'));
        }
        $id = $article->id;
        $comment->article_id = $id;
        if ($comment->fill($request->input())->save()) {

        //if Admin reply  and when aget reply on comment if agent have kb access that time comment automatic approved
        $authAgent = \Auth::user();
        $role=User::where('email',$comment->email)->value('role');
        
        if($role && $role == 'admin' || ($authAgent && $authAgent->role == 'agent' && User::has('access_kb'))){
         Comment::where('id',$comment->id)->update(['status'=>1]);
        }
        //convert to utc format
        $timestamp = Comment::where('id',$comment->id)->value('created_at');
        $changeTime= changeTimezoneForDatetime($timestamp->toDateTimeString(),System::first()->time_zone, 'UTC');
        $saveTime =$changeTime->toDateTimeString();
        Comment::where('id',$comment->id)->update(['created_at'=>$saveTime,'updated_at'=>$saveTime]);
        
        $checkstatus=Comment::where('id',$comment->id)->value('status');
        if($checkstatus == 0){
            return successResponse(Lang::get('lang.comment_posted_successfully_and_needs_approval_from_admin'));
        }
        return successResponse(Lang::get('lang.comment_posted_successfully'));

        
          } else {
            return errorResponse(Lang::get('lang.sorry_not_processed'));
        }

        } catch (Exception $ex) {
            
            return errorResponse($ex->getMessage());
        }
    }
  /**
     * 
     * @param type $name
     * @param Page $page
     * @return type view
     */
    public function getPage($name, Page $page)
    {
        $pages = Page::where('slug', '=', $name)->where('status', '1')->where('visibility', '1')->count();
        if ($pages == 0) {
            return redirect('/')->with('fails', Lang::get('lang.this_page_not_available_now'));
        }
        $page = $page->where('slug', $name)->first();
        if ($page) {
            return view('themes.default1.client.kb.article-list.pages', compact('page'));
        } else {
            return Redirect::back()->with('fails', Lang::get('lang.sorry_not_processed'));
        }
    }

    public static function port()
    {
        $setting = Settings::whereId('1')->first();
        Config::set('mail.port', $setting->port);
    }

    public static function host()
    {
        $setting = Settings::whereId('1')->first();
        Config::set('mail.host', $setting->host);
    }

    public static function encryption()
    {
        $setting = Settings::whereId('1')->first();
        Config::set(['mail.encryption' => $setting->encryption, 'mail.username' => $setting->email]);
    }

    /**
     * 
     */
    public static function email()
    {
        $setting = Settings::whereId('1')->first();
        Config::set(['mail.from' => ['address' => $setting->email, 'name' => 'asd']]);
    }

    /**
     * 
     */
    public static function password()
    {
        $setting = Settings::whereId('1')->first();
        Config::set(['mail.password' => $setting->password, 'mail.sendmail' => $setting->email]);
    }

    /**
     * 
     * @param Article $article
     * @param Category $category
     * @param Relationship $relation
     * @param Settings $settings
     * @return type view
     */
    public function getCategoryList(Article $article, Category $category, Relationship $relation, Settings $settings)
    {

        

        /* direct to view with $article_id */
        return view('themes.default1.client.kb.article-list.categoryList');
    }


    /**
     * 
     * @return type view
     */
    public function clientProfile()
    {
        $user = Auth::user();

        return view('themes.default1.client.kb.article-list.profile', compact('user'));
    }

    /**
     * @deprecated
     * @param type $id
     * @param \App\Http\Controllers\Client\kb\ProfileRequest $request
     * @return type
     */
    public function postClientProfile($id, ProfileRequest $request)
    {
        $user = Auth::user();
        $user->gender = $request->input('gender');
        $user->save();
        if ($user->profile_pic == 'avatar5.png' || $user->profile_pic == 'avatar2.png') {
            if ($request->input('gender') == 1) {
                $name = 'avatar5.png';
                $destinationPath = 'lb-faveo/dist/img';
                $user->profile_pic = $name;
            } elseif ($request->input('gender') == 0) {
                $name = 'avatar2.png';
                $destinationPath = 'lb-faveo/dist/img';
                $user->profile_pic = $name;
            }
        }
        if (Input::file('profile_pic')) {
            //$extension = Input::file('profile_pic')->getClientOriginalExtension();
            $name = Input::file('profile_pic')->getClientOriginalName();
            $destinationPath = 'lb-faveo/dist/img';
            $fileName = rand(0000, 9999) . '.' . $name;
            //echo $fileName;
            Input::file('profile_pic')->move($destinationPath, $fileName);
            $user->profile_pic = $fileName;
        } else {
            $user->fill($request->except('profile_pic', 'gender'))->save();

            return redirect('guest')->with('success', Lang::get('lang.profile_updated_sucessfully'));
        }
        if ($user->fill($request->except('profile_pic'))->save()) {
            return redirect('guest')->with('success', Lang::get('lang.sorry_not_proprofile_updated_sucessfullycessed'));
        }
    }

    /**
     * 
     * @param type $id
     * @param ProfilePassword $request
     * @return type
     */
    public function postClientProfilePassword($id, ProfilePassword $request)
    {
        $user = Auth::user();
        //echo $user->password;
        if (Hash::check($request->input('old_password'), $user->getAuthPassword())) {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            return redirect()->back()->with('success', Lang::get('lang.password_updated_sucessfully'));
        } else {
            return redirect()->back()->with('fails', Lang::get('lang.password_was_not_updated'));
        }
    }

   
   
}
