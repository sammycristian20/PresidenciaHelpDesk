<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\User;
use Exception;

/**
 * WorkflowController
 * In this controller in the CRUD function for all the workflow applied in faveo.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class WorkflowController extends Controller
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
        // checking admin roles
        $this->middleware('roles');
    }
    /**
     * Display a listing of all the workflow.
     *
     * @return type
     */
    public function index()
    {
        try {
            return view('themes.default1.admin.helpdesk.manage.workflow.index');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new workflow.
     *
     * @return type Response
     */
    public function create()
    {
        try {
            return view('themes.default1.admin.helpdesk.manage.workflow.create');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Editing the details of the banned users.
     *
     * @param type $id
     * @param User $ban
     *
     * @return type Response
     */
    public function edit($id)
    {
        try {

            return view('themes.default1.admin.helpdesk.manage.workflow.edit');
        } catch (Exception $e) {

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }
}
