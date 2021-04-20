<?php

namespace App\Http\Controllers\Agent\helpdesk;

// controllers
use App\Facades\Attach;
use App\Http\Controllers\Controller;
// requests
use App\Http\Controllers\Utility\FormController;
use App\Http\Requests\helpdesk\OrganizationRequest;
/* include organization model */
use App\Http\Requests\helpdesk\OrganizationUpdate;
// models
/* Define OrganizationRequest to validate the create form */
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\OrganizationDepartment;
use App\Model\helpdesk\Agent_panel\User_org;
/* Define OrganizationUpdate to validate the create form */

use App\Model\helpdesk\Settings\FileSystemSettings;
use App\User;
// classes
use Exception;
use Lang;
use Illuminate\Http\Request;
use Datatables;
use DB;
use File;
use Auth;
use App\FaveoStorage\Controllers\StorageController;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Form\CustomFormValue;
use App\Http\Requests\helpdesk\Organisation\OrganisationCreateRequest;
use App\Http\Requests\helpdesk\Organisation\OrganisationEditRequest;
use App\Http\Requests\helpdesk\Organisation\OrganizationDepartmentRequest;
use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Model\helpdesk\Settings\CommonSettings;

/**
 * OrganizationController
 * This controller is used to CRUD organization detail.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class OrganizationController extends Controller {

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */
    public function __construct() {
        // checking for authentication
        $this->middleware('auth');
        // checking if the role is agent
        $this->middleware('role.agent');
        $this->middleware('directory.organization');
    }

   /**
     * Show Organization index page.
     *  
     * @return type html
     */
    public function index()
    {
      try {

        return view('themes.default1.agent.helpdesk.organization.index');

      } catch (Exception $ex) {

        return redirect()->back()->with('fails', $ex->getMessage());
      }
    }
    /**
     * This function is used to display the list of Organizations data
     *
     * @return type json
     */
    public function orgListData(Request $request)
    {
        try {

            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;
            $sortBy = ($request->input('sort-by')) ? $request->input('sort-by') : 'id';
            
            $search = $request->input('search-query');
            
            $orderBy = ($request->input('order')) ? $request->input('order') : 'desc';
            $baseQuery = Organization::select('id', 'name', 'website', 'phone','created_at','updated_at')->withCount('activeUsers')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('website', 'LIKE', '%' . $search . '%')
                        ->orWhere('phone', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Show the form for creating a new organization.
     *
     * @return type Response
     */
    public function create() {
        try {
              if (!User::has('access_organization_profile')){
                  return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
              }
            return view('themes.default1.agent.helpdesk.organization.create');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }



    /**
     * Display the specified organization.
     *
     * @param type              $id
     * @param type Organization $org
     *
     * @return html
     */
    public function show($id, Organization $org) {
        try {
              if (!User::has('access_organization_profile')){
                  return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
              }

            /* To view page */
            return view('themes.default1.agent.helpdesk.organization.show');

        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified organization.
     *
     * @param int   $id
     *
     * @return type html
     */
    public function edit($id, Organization $org) {
        try {
              if (!User::has('access_organization_profile')){
                  return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
              }

            /* To view page */
            return view('themes.default1.agent.helpdesk.organization.edit');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    

   /**
     * Delete a specified organization from storage.
     *
     * @param type int $id
     *
     * @return type Redirect
     */
    public function destroy($orgId)
    {
        try {
            
            $organization = Organization::whereId($orgId)->first();

            if(! $organization)
            {
              return errorResponse(Lang::get('lang.organization_not_found'));
            }
            $organization->delete();

            return successResponse(Lang::get('lang.organization_deleted_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    public function getHrdname(Request $request, $org_id) {
        try {
            $term = trim($request->q);
            if (empty($term)) {
                return \Response::json([]);
            }

            $orgs = Organization::select('id', 'domain')->where('id', '=', $org_id)->first();
            $user_orga_relations = \App\Model\helpdesk\Agent_panel\User_org::where('org_id', '=', $orgs->id)->pluck('user_id')->toArray();
            $users = $user_orga_relations;
            if ($orgs['domain'] != '') {
                $str = str_replace(",", '|@', '@' . $orgs['domain']);
                $domain_users = \App\User::select('id')->where('role', '=', 'user')->whereRaw("email REGEXP '" . $str . "'")->whereNOtIn('id', $users);
                $domain_users = $domain_users->where('is_delete', '!=', 1)->get()->toArray();
                if (count($domain_users) > 0) {
                    $users = array_merge($users, array_column($domain_users, 'id'));
                }
            }

           $org_user = \App\User::wherein('id', $users)->where('active', '=', 1)->where('is_delete','!=',1)
                                                 ->when($term, function($q) use($term) {
                                $q->where(function($org_user) use($term) {
                                    $org_user->select('email','id', 'first_name', 'last_name', 'profile_pic')
                                    ->where('first_name', 'LIKE', '%' . $term . '%')
                                    ->orwhere('last_name', 'LIKE', '%' . $term . '%')
                                    ->orwhere('user_name', 'LIKE', '%' . $term . '%')
                                    ->orwhere('email', 'LIKE', '%' . $term . '%');
                                });
                                  })->get();

           $formatted_tags = [];
            foreach ($org_user as $org) {
                $formatted_orgs[] = ['id' => $org->id, 'text' => $org->email,'profile_pic' => $org->profile_pic,'first_name'=>$org->first_name,'last_name'=>$org->last_name ];
            }
            return \Response::json($formatted_orgs);
        } catch (\Exception $e) {
            // returns if try fails with exception meaagse
            return \Response::json([]);
        }
    }

  /**
 * This method create organization manager
 * @param Request $request
 * @return type Redirect
 */
public function createOrgManager(Request $request)
    {
        if (!User::has('access_organization_profile')){
                  return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
              }
        try {
            $orgManagerUserIds = $request->user;

            if (!count($orgManagerUserIds)) {
                return redirect()->back()->with('fails1', Lang::get('lang.please_select_manager'));
            }
            else{
            User_org::where('org_id', $request->org_id)->update(['role' => 'members']);

               foreach ($orgManagerUserIds as $userid) {
                   User_org::where('org_id', $request->org_id)->where('user_id', $userid)->update(['role' => 'manager']);
                }
            return redirect()->back()->with('success1', Lang::get('lang.organization_updated_successfully'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
 * This method edit organization manager
 * @param Request $request
 * @return type Redirect
 */
    public function editOrgManager(Request $request)
    {
        if (!User::has('access_organization_profile')){
                  return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
              }
        try {
            $orgManagerUserIds = $request->user;
            User_org::where('org_id', $request->org_id)->update(['role' => 'members']);
            if(count( $orgManagerUserIds)){
                foreach ($orgManagerUserIds as $userid) {
                User_org::where('org_id', $request->org_id)->where('user_id', $userid)->update(['role' => 'manager']);
               }
            }

            return redirect()->back()->with('success1', Lang::get('lang.organization_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Display the specified organization.
     *
     * @param type              $id
     * @param type Organization $org
     *
     * @return type view
     */
    public function HeadDelete(Request $request) {
        try {
            if (!User::has('access_organization_profile')){
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }
            $user_id = $request->user_id;
            $org_head = User_org::where('org_id', '=', $orgs_id)->where('user_id', '=', $user_id)->delete();
            // return redirect('themes.default1.agent.helpdesk.organization.show')->with('success', Lang::get('lang.organization_maneger_delete_successfully'));
            return 'maneger successfully delete';
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

   
    public function getOrgAjax(Request $request) {
        $org = new Organization();
        $q = $request->input('term');
        $orgs = $org->where('name', 'LIKE', '%' . $q . '%')
                ->select('name as label', 'id as value')
                ->get()
                ->toJson();
        return $orgs;
    }

    /**
     * This function is used autofill organizations name .
     *
     * @return datatable
     */
    public function organizationAutofill() {
        return view('themes.default1.agent.helpdesk.organization.getautocomplete');
    }
    /**
      * This method store organization information
      * @param OrganizationRequest $request
      * @return type
      */
    public function createOrgApi(OrganisationCreateRequest $request)
     {

         try {
             $domainName = ($request->organisation_domain_name) ? implode(",", $request->organisation_domain_name) : "";
             $default = ['organisation_name', 'phone', 'website', 'address', 'head', 'description', 'organisation_domain_name', 'department', 'organisation_logo'];
             $extra = $request->except($default);
             Organization::create(['name' => $request->organisation_name, 'phone' => $request->phone,'address'=>$request->address, 'internal_notes' => $request->description, 'domain' => $domainName]);
             $orgId = Organization::orderBy('id', 'desc')->value('id');
             $org = Organization::where('id',$orgId)->first();
             if ($request->organisation_department) {
                 foreach ($request->organisation_department as $departmentName) {
                     OrganizationDepartment::create(['org_id' => $orgId, 'org_deptname' => $departmentName, 'business_hours_id' => null]);
                 }
             }
             CustomFormValue::updateOrCreateCustomFields($extra, $org);

             //unfortunately organization logo is coming as array from frontend
             $arrayOfLogos = $request->file('organisation_logo');

             $organizationLogoPath = ($arrayOfLogos)
                 ? Attach::put('organization_logo', reset($arrayOfLogos), null, null, true, 'public')
                 : '';

             $org = Organization::where('id', $org->id)->first();
             $org->logo =  $organizationLogoPath ? Attach::getUrlForPath($organizationLogoPath, null, 'public') : '';
             $org->save();

             //below domain link we have to think another way
             if($domainName){
              //update organization Domain Linking
              $this->organizationDomainLinking();

             }
            $checkUserInOrganization = User_org::where('org_id',$orgId)->where('user_id',$request->user_id)->count();

            if(!$checkUserInOrganization && $request->user_id){

              $assignOrganization = new User_org();
              $assignOrganization->org_id = $orgId;
              $assignOrganization->user_id = $request->user_id;
              $assignOrganization->role = 'members';
              $assignOrganization->save();
            }

             return successResponse(Lang::get('lang.organization_saved_successfully'));
         } catch (\Exception $ex) {
             return errorResponse($ex->getMessage());
         }
     }

    /**
     * This methode return organization information
     * string type $id of organization
     * @return type json
     */
    public function editOrgApi($id)
    {
    try {
            $customFields = CustomFormValue::getCustomFields(Organization::whereId($id)->first());
            $org = Organization::whereId($id)->select('id','name as organisation_name','phone','website','address','head','internal_notes as description','domain','logo','logo_driver')->first();

            $orgDomains = ($org->domain != "")  ? explode(",", $org->domain) : [];

            $orgDept = OrganizationDepartment::where('org_id', $id)->pluck('org_deptname')->take(100);

            $logo=[];
            if ($org->logo) {
                $type = pathinfo($org->logo, PATHINFO_EXTENSION);

                //PHP supports one error control operator: the at sign (@). When prepended to an expression in PHP, any error messages that might be generated by that expression will be ignored.
                
                $image = @file_get_contents($org->logo);

                $logo = ["file"=>'data:image/' . $type . ';base64,' . base64_encode($image), "filename"=>basename($org->logo)];
            }

            $orgInfo = (['organisation_domain_name'=>$orgDomains, 'organisation_logo'=>$logo,'organisation_department' => $orgDept]);
            
            $organizationDetails = array_merge( $org->toArray(),$orgInfo,$customFields);

            $editForm = (new FormController())->getFormWithEditValues($organizationDetails, 'organisation','edit', 'agent', $id);

            return successResponse('',$editForm);
        } catch (\Exception $ex) {
          return errorResponse( $ex->getMessage());
        }
    }

    /**
     * THis methode update organization information
     * @param type $id
     * @param OrganizationUpdate $request
     * @return type
     */
    public function updateOrgApi($id, OrganisationEditRequest $request)
    {
        try {
            $domainName = ($request->organisation_domain_name != "") ? implode(",", $request->organisation_domain_name) : "";
            $default = ['organisation_name', 'phone', 'website', 'address', 'head', 'description', 'organisation_domain_name', 'department', 'organisation_logo'];
            $extra = $request->except($default);
            $org = Organization::where('id', $id)->update(['name' => $request->organisation_name, 'phone' => $request->phone, 'address' => $request->address, 'head' => $request->head, 'internal_notes' => $request->description, 'domain' => $domainName]);
            if ($request->organisation_department != "") {
                $organizationDeptIds = OrganizationDepartment::whereIn('org_deptname', $request->organisation_department)->pluck('id');
                OrganizationDepartment::select('id')->whereNotIn('id', $organizationDeptIds)->where('org_id', $id)->delete();
                $departmentNames = $request->organisation_department;
                foreach ($departmentNames as $departmentName) {
                    OrganizationDepartment::updateOrCreate(['org_deptname' => $departmentName, 'org_id' => $id]);
                }
            } else {
                OrganizationDepartment::where('org_id', $id)->delete();
            }
            $orgDeptIds = User_org::where('id', '!=', 0)->where('org_department', '!=', null)->pluck('org_department')->toArray();
            if ($orgDeptIds) {
                User_org::whereNotIn('id', $orgDeptIds)->update(['org_department' => null]);
            }
            $org = Organization::where('id', $id)->first();
            // updating custom fields
            CustomFormValue::updateOrCreateCustomFields($extra, $org);

            if ($request->file('organisation_logo')) {
                //unfortunately organization logo is coming as array from frontend
                $arrayOfLogos = $request->file('organisation_logo');

                $organizationLogoPath = Attach::put('organization_logo', reset($arrayOfLogos), null, null, true, 'public');

                $org = Organization::where('id', $id)->first();
                $org->logo = $organizationLogoPath ? Attach::getUrlForPath($organizationLogoPath, null, 'public') : '';
                $org->save();
            } else {
                Organization::where('id', $id)->update(['logo' => ""]);
            }

             //below domain link we have to think another way
             if($domainName){
             
              //update organization Domain Linking
              $this->organizationDomainLinking();

             }
           return successResponse(Lang::get('lang.organization_updated_successfully'));
        } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }


/**
 * this method link between user and organization based on organization domain
 * @return boolean
 */
    public function organizationDomainLinking()
    {
        $userIds = User::where('role', 'user')->pluck('id')->toArray();
        foreach ($userIds as $userId) {
            //associate user to organization base on domain match
            UserController::domainConnection($userId);
        }
        return true;
    }

    /**
     * This method Edit organization Department
     * @param type $orgId of the Organization
     * @param \Illuminate\Http\Request $request
     * return Type json
     */
    public function EditOrgDept($orgId, Request $request)
    {
        if (!User::has('access_organization_profile')){
            return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
        }
        try {
            $validator = Validator::make($request->all(), [
                 'org_deptname'   => 'required|max:25',
            ]);
          if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $check=OrganizationDepartment::where('org_id', $orgId)->where('id','!=',$request->input('deptId'))->where('org_deptname',$request->input('org_deptname'))->count();

            if($check == 1){
                return response()->json(['error' => [Lang::get('lang.the_org_deptname_has_already_been_taken')]]);
            }

            $managerId = ($request->input('managerId')) ? $request->input('managerId')[0] : null;
            OrganizationDepartment::where('org_id', $orgId)->where('id', $request->input('deptId'))->update(['org_deptname' => $request->input('org_deptname'), 'business_hours_id' => $request->input('businessHoursId'), 'org_dept_manager' => $managerId]);
            return response()->json(['success' => Lang::get('lang.updated_successfully')]);
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

/**
     * This method return user list of particular organization
     * @param type $orgId of Organization
     * @return type json
     */
    public function getOrgUserList(int $orgId, Request $request)
    {
        try {
            $userId = User_org::where('org_id', $orgId)->pluck('user_id')->toArray();
            $pagination = $request->input('limit') ?  : 10;
            $sortBy = $request->input('sort-by') ?  : 'id';
            $search = $request->input('search-query');
            $orderBy = $request->input('order') ? : 'desc';
            
            $baseQuery = User::whereIn('id', $userId)->select('id', 'first_name', 'last_name', 'user_name', 'phone_number', 'email', 'active', 'email_verify', 'mobile_verify', 'is_delete','updated_at', 'role')->where('is_delete', 0)->orderBy($sortBy, $orderBy);
            
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('updated_at', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }


    /**
     *
     * @param type $orgId of Organization
     * @param Request $request
     * @return type
     */
    public function getOrgDeptList(int $orgId, Request $request)
    {
        try {
            $orgDeptIds = OrganizationDepartment::where('org_id', $orgId)->pluck('id')->toArray();
            $pagination = $request->input('limit') ?  : 10;

            $sortBy = $request->input('sort-by') ?  : 'id';
            
            $search = $request->input('search-query');
            
            $orderBy = $request->input('order') ?  : 'desc';

            $baseQuery = OrganizationDepartment::whereIn('id', $orgDeptIds)->with(['businessHour','manager'])->select('id', 'org_deptname', 'business_hours_id', 'org_dept_manager')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('org_deptname', 'LIKE', '%' . $search . '%')
                        ->orWhere('org_dept_manager', 'LIKE', '%' . $search . '%')
                        ->orWhere('business_hours_id', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);

            return successResponse($searchQuery);

        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    /**
     * This method create organization department
     * @param OrganizationDepartmentRequest $request
     * @return string
     */
    public function postOrgDepartment(OrganizationDepartmentRequest $request)
    {
        try {
            OrganizationDepartment::updateOrCreate(['id' => $request->org_dept_id], ['org_id' => $request->org_id, 'org_deptname' => $request->org_deptname, 'business_hours_id' => $request->business_hours_id, 'org_dept_manager' => $request->org_dept_manager]);

         
           $outputMessage = $request->org_dept_id ? Lang::get('lang.updated_successfully') : Lang::get('lang.organization_department_created_successfully');
           
        return successResponse($outputMessage);
            
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
    /**
     * this method create organization department
     * @param type $orgId of Organization
     * @param Request $request
     * @return string
     */
    public function createNewOrgDept(int $orgId, Request $request)
    {
        if (!User::has('access_organization_profile')){
            return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
        }

        try {
            $validator = Validator::make($request->all(), [
                        "org_deptname" => 'required|max:25',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $check=OrganizationDepartment::where('org_id', $orgId)->where('org_deptname',$request->input('org_deptname'))->count();

            if($check == 1){
                return response()->json(['error' => [Lang::get('lang.the_org_deptname_has_already_been_taken')]]);
            }


            //Store organization department
            $managerId = ($request->input('managerId')) ? $request->input('managerId')[0] : null;

            OrganizationDepartment::create(['org_id' => $orgId, 'org_deptname' => $request->input('org_deptname'), 'business_hours_id' => $request->input('businessHoursId'), 'org_dept_manager' => $managerId]);

            return response()->json(['success' => Lang::get('lang.organization_department_created_successfully')]);
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * this method deleting organization department
     * @param Request $request
     * @return string
     */
    public function deleteOrgDept($id)
    {
        try {
            if (!User::has('access_organization_profile')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }
            $orgDept = OrganizationDepartment::where('id', $id)->first();
            $orgDept->delete();
            return successResponse(Lang::get('lang.organization_department_deleted_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

      public function getOrgDepartmentInfo($orgDeptId)
    {
        try{
           $data = OrganizationDepartment::where('id',$orgDeptId)->with(['businessHour','manager'])->select('id', 'org_deptname','business_hours_id', 'org_dept_manager')->first()->toArray();
           if( $data['manager']){
            $data['manager']['name'] = $data['manager']['meta_name'];
           }
           
           return successResponse('',$data);
           } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }


    /**
     * This method return user list of organization department
     * @param type $orgId of the Organization
     * @param type $deptId of Organizationdepartment
     * @param Request $request
     * @return type json
     */
    public function getOrgDeptUserList(int $orgId, int $deptId, Request $request)
    {
        try {
            $deptId = OrganizationDepartment::where('id', $deptId)->value('id');
            $userId = User_org::where('org_id', $orgId)->where('org_department', $deptId)->pluck('user_id')->toArray();

            $pagination = $request->input('limit') ?  : 10;
            $sortBy = $request->input('sort-by') ?  : 'id';
            
            $search = $request->input('search-query');
            
            $orderBy = $request->input('order') ?  : 'desc';

            $baseQuery = User::whereIn('id', $userId)->select('id', 'first_name', 'last_name', 'user_name', 'email', 'active')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('first_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * 
     * @param Request $request
     * @param type $orgId
     * @return type
     */
    public function getOrgManager($orgId, Request $request)
    {
        try {
            $limit = $request->input('limit');
            $search = trim($request->input('search-query'));
            $orgs = Organization::where('id', $orgId)->select('id', 'domain')->first();
            $userIds = User_org::where('org_id', $orgId)->pluck('user_id')->toArray();
            if ($orgs['domain']) {
                $str = str_replace(",", '|@', '@' . $orgs['domain']);
            $domainUsers = User::where('role', 'user')->whereRaw("email REGEXP '" . $str . "'")->whereNOtIn('id', $userIds)->where('is_delete', '!=', 1)->select('id')->get()->toArray();
                if (count($domainUsers) > 0) {
                    $userIds = array_merge($userIds, array_column($domainUsers, 'id'));
                }
            }
            $organizationUser = User::wherein('id', $userIds)->where('active',1)->where('is_delete', '!=', 1)
                ->when($search, function($q) use($search) {
                    $q->where(function($orgUser) use($search) {
                        $orgUser->select('email', 'id', 'first_name', 'last_name', 'profile_pic')
                        ->where('first_name', 'LIKE', '%' . $search . '%')
                        ->orwhere('last_name', 'LIKE', '%' . $search . '%')
                        ->orwhere('user_name', 'LIKE', '%' . $search . '%')
                        ->orwhere('email', 'LIKE', '%' . $search . '%');
                    });
                })
                ->paginate($limit);

            $organizationUser->getCollection()->transform(function($user){
                    $user->name = $user->meta_name;
                    return $user;
                });

            return successResponse('',$organizationUser);
          } catch (\Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }
    /**
     * This method create organization manager
     * @param Request $request
     * @return type Redirect
     */
    public function postOrgManager(Request $request)
    {
        if (!User::has('access_organization_profile')) {
            return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
        }
        try {
            if (!count($request->user)) {
                return errorResponse(Lang::get('lang.please_select_manager'));
            }
            User_org::where('org_id', $request->org_id)->update(['role' => 'members']);
            foreach ($request->user as $userid) {
                User_org::where('org_id', $request->org_id)->where('user_id', $userid)->update(['role' => 'manager']);
            }
            return successResponse(Lang::get('lang.organization_updated_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method return Organization data info
     * @param int $orgId
     * @return json
     */
    public function organizationViewApi($orgId)
    {
        try {
            if (!User::has('access_organization_profile')) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission_denied'));
            }
            /* select the field by id  */
            $organizationInfo = Organization::
                    with(['customFieldValues'])->whereId($orgId)->first();

            $organizationInfo->logo = $organizationInfo->logo ? : NULL;

            $orgDeptStatus = CommonSettings::where('option_name', 'micro_organization_status')->value('status');

            $organizationInfo['OrganizationDepartmentStatus'] = $orgDeptStatus ? true : false;

            $organizationManagerId = User_org::where('org_id', $orgId)->where('role', '!=', 'members')->pluck('user_id')->toArray();

            $organizationManagerInfo = user::whereIn('id', $organizationManagerId)->select('id', 'email', 'first_name', 'last_name', 'profile_pic','phone_number')->get();

            foreach ($organizationManagerInfo as $user) {
              
              $user['name'] = $user['meta_name'];
            }


            $data['organization'] = $organizationInfo;

            $data['manager'] = $organizationManagerInfo;


            return successResponse('', $data);

        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /*
    This method store image file in public folder 
    */
    private function imageStore($files){

        $rootPath = public_path('uploads' . DIRECTORY_SEPARATOR . 'company');
        $logoName = $files[0]->getClientOriginalName();
        $fileName = rand(0000, 9999) . '_' . $logoName;
        uploadInLocal($files[0], $rootPath, $fileName, $rootPath);
        return $fileName;
    }
}
