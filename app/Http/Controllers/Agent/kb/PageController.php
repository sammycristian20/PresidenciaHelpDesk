<?php

namespace App\Http\Controllers\Agent\kb;

use App\Http\Controllers\Controller;
// request
use App\Http\Requests\kb\PageRequest;
use App\Model\kb\Page;
// classes
use Exception;
use Illuminate\Http\Request;
use Lang;

/**
 * PageController
 * This controller is used to CRUD Pages.
 *
 * @author      Arindam Jana <arindam.jana@ladybirdweb.com>
 */
class PageController extends Controller
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

    public function __construct()
    {
        // checking authentication
        $this->middleware('auth');
        SettingsController::language();
    }

    /**
     * Display the list of pages.
     *
     * @return type html
     */
    public function index()
    {
        try {

            return view('themes.default1.agent.kb.pages.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * this method return all a page data with pagination
     * @param Request $request
     * @return type json
     */
    public function getData(Request $request)
    {
        try {

            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;
            $sortBy = ($request->input('sort')) ? $request->input('sort') : 'id';
            $search = $request->input('search');
            $orderBy = ($request->input('order')) ? $request->input('order') : 'desc';

            $baseQuery = Page::select('id', 'name', 'description', 'slug', 'status', 'created_at')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
            })
                ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method view create page
     *
     * @return type html
     */
    public function create()
    {
        try {

            return view('themes.default1.agent.kb.pages.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method store or update kb page
     * @param type PageRequest $request
     * @return type Response
     */
    public function store(PageRequest $request)
    {
        try {

            Page::updateOrCreate(
                ['id' => $request->pageid], ['name' => $request->name, 'slug' => $request->slug, 'visibility' => $request->visibility, 'status' => $request->status, 'description' => $request->description]
            );
            $pageId = $request->pageid ? $request->pageid : Page::orderBy('id', 'desc')->value('id');

            $checkSeo = Page::where('id', $pageId)->first();
            $checkSeo->meta_description = (!$checkSeo->meta_description) ? substr(strip_tags($checkSeo['description']), 0, 160) . "..." : $request->meta_description;
            $checkSeo->seo_title = (!$checkSeo->meta_description) ? $checkSeo->name : $request->seo_title;
            $checkSeo->save();

            $outputMessage = ($request->pageid) ? Lang::get('lang.your_page_updated_successfully') : Lang::get('lang.page_saved_successfully');

            return successResponse($outputMessage);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method view edit page

     * @return type html
     */
    public function edit()
    {
        try {

            return view('themes.default1.agent.kb.pages.edit');
        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return kb page info
     * @param type int $pageid
     * @return type json
     */
    public function editApi($pageid)
    {
        try {

            $page = Page::whereId($pageid)->first();

            return successResponse(['page' => $page]);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method delete page
     * @param type int $pageId
     * @return type Response
     */
    public function destroy($pageId)
    {
        try {

            // get the page to be deleted
            $page = Page::whereId($pageId)->first();
            $page->delete();

            return successResponse(Lang::get('lang.page_deleted_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

}
