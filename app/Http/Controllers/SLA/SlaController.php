<?php

namespace App\Http\Controllers\SLA;

use App\Http\Controllers\Controller;
use Exception;

/**
 * SlaController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class SlaController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles');
    }

    /**
     * Display a listing of the resource.
     *
     * @param type Sla_plan $sla
     *
     * @return type Response
     */
    public function index()
    {
        try {
            return view('themes.default1.admin.helpdesk.manage.sla.index');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return type Response
     */
    public function create()
    {
        try {
            /* Direct to Create Page */
            return view('themes.default1.admin.helpdesk.manage.sla.create');
        } catch (Exception $e) {

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param type int      $id
     * @param type Sla_plan $sla
     *
     * @return type Response
     */
    public function edit($id)
    {
        try {
            return view('themes.default1.admin.helpdesk.manage.sla.edit');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

}
