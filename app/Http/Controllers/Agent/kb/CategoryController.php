<?php

namespace App\Http\Controllers\Agent\kb;

use App\Http\Controllers\Controller;
use App\Http\Requests\kb\CategoryRequest;
use App\Model\kb\Category;
use App\Model\kb\Relationship;
use Exception;
use Illuminate\Http\Request;
use Lang;

/**
 * CategoryController
 * This controller is used to CRUD category.
 *
 * @author       Arindam Jana <arindam.jana@ladybirdweb.com>
 */
class CategoryController extends Controller
{

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent/admin.
     *
     * @return void
     */

    public function __construct()
    {
        // checking authentication
        $this->middleware('auth');
        // checking roles

        SettingsController::language();
    }

    /**
     * Show Category index page.
     * @return html
     */
    public function index()
    {
        try {
            return view('themes.default1.agent.kb.category.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return all Category Info with pagination
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

            $baseQuery = Category::select('id', 'name', 'status', 'slug', 'description', 'display_order')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('display_order', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%');
            })
                ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {

            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Create a Category.
     *
     * @return type html
     */
    public function create(Category $category)
    {
        /* Get the all attributes in the category model */

        try {

            return view('themes.default1.agent.kb.category.create');

        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method store and update category
     *
     * @param type CategoryRequest $request
     *
     * @return type message
     */
    public function store(CategoryRequest $request)
    {
        try {

            Category::updateOrCreate(
                ['id' => $request->id], ['name' => $request->name, 'slug' => str_slug($request->name, '-'), 'status' => $request->status, 'display_order' => $request->display_order, 'description' => $request->description]
            );

            $outputMessage = ($request->id) ? Lang::get('lang.category_updated_successfully') : Lang::get('lang.category_saved_successfully');
            return successResponse($outputMessage);

        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     * @return type html
     */
    public function edit()
    {
        try {
            return view('themes.default1.agent.kb.category.edit');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return category info
     * @param int $categoryId
     * @return type json
     */
    public function editApi($categoryId)
    {
        try {
            $category = Category::whereId($categoryId)->first();
            return successResponse(['category' => $category]);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param type              $categoryId
     * @param type Category     $category
     * @param type Relationship $relation
     *
     * @return type Response
     */
    public function destroy($categoryId, Category $category, Relationship $relation)
    {
        $relation = $relation->where('category_id', $categoryId)->first();
        if ($relation != null) {
            return errorResponse(Lang::get('lang.category_not_deleted'));
        } else {
            $category = $category->whereId($categoryId)->first();
            try {
                $category->delete();

                return successResponse(Lang::get('lang.category_deleted_successfully'));
            } catch (Exception $ex) {
                return errorResponse($ex->getMessage());
            }
        }
    }

}
