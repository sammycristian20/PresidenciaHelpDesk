<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;

/**
 * FormController
 * This controller is used to CRUD Custom Forms.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class FormController extends Controller {

    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * create a new form.
     *
     * @return Response
     */
    public function create() {
        try {
            return view('themes.default1.admin.helpdesk.manage.form.form');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function user(){
        return view('themes.default1.admin.helpdesk.manage.form.user-form');
    }
}
