<?php

namespace App;

use App\Exceptions\DuplicateUserException;
use App\Facades\Attach;
use App\Traits\Observable;
use App\FaveoReport\Models\UserTodo;
use Hashids\Hashids;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use App\Model\helpdesk\Settings\System;
use App\Model\kb\Timezone;
use Laravel\Passport\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use function Clue\StreamFilter\fun;
use Config;
use App\Exceptions\AgentLimitExceededException;
use App\Model\helpdesk\Agent\UserPermission;
use Exception;
use Auth;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract, JWTSubject
{
    use Authenticatable, CanResetPassword, Notifiable, HasApiTokens, Observable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_name', 'email', 'password', 'active', 'first_name', 'last_name', 'ext', 'mobile', 'profile_pic',
        'phone_number', 'company', 'agent_sign', 'account_type', 'account_status', 'user_language',
        'assign_group', 'primary_dpt', 'agent_tzone', 'daylight_save', 'limit_access',
        'directory_listing', 'vacation_mode', 'role', 'internal_note', 'country_code', 'not_accept_ticket', 'is_delete', 'mobile_verify',
        'email_verify', 'location', 'department', 'import_identifier','google2fa_secret','is_2fa_enabled','google2fa_activation_date', 'last_login_at', 'delete_account_requested', 'processing_account_disabling', 'iso'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'hash_ids', 'google2fa_secret', 'mobile_verify', 'email_verify'];
    
    protected $appends = ['hash_ids', 'full_name', 'meta_name', 'email_verified', 'mobile_verified'];

    /**
     * Attributes which requires html purification. All attributes which allows HTML should be added to it
     * @var array
     */
    protected $htmlAble = ['agent_sign'];

    public function setFirstNameAttribute($value){
        $this->attributes['first_name'] = strip_tags($value);
    }

    public function setLastNameAttribute($value){
        $this->attributes['last_name'] = strip_tags($value);
    }

    public function getProfilePicAttribute($value)
    {
        return $value ?: ($this->attributes['email'] ? \Gravatar::src($this->attributes['email']) : assetLink('image', 'contacthead'));
    }

    
    public function userExtraField()
    {
        $related    = 'App\UserAdditionalInfo';
        $foreignKey = 'owner';
        return $this->hasMany($related, $foreignKey);
    }
    public function getOrganizationRelation()
    {
        $related       = "App\Model\helpdesk\Agent_panel\User_org";
        $user_relation = $this->hasMany($related, 'user_id');
        $relation      = $user_relation->first();
        if ($relation) {
            $org_id = $relation->org_id;
            $orgs   = new \App\Model\helpdesk\Agent_panel\Organization();
            $org    = $orgs->where('id', $org_id);
            return $org;
        }
    }
    public function getOrganization()
    {
        $name = "";
        if ($this->getOrganizationRelation()) {
            $org = $this->getOrganizationRelation()->first();
            if ($org) {
                $name = $org->name;
            }
        }
        return $name;
    }
    public function getOrgWithLink()
    {
        $name = "";
        $org  = $this->getOrganization();
        if ($org !== "") {
            $orgs = $this->getOrganizationRelation()->first();
            if ($orgs) {
                $id   = $orgs->id;
                $name = "<a href=" . url('organizations/' . $id) . ">" . ucfirst($org) . "</a>";
            }
        }
        return $name;
    }

    public function setEmailAttribute($value)
    {
      // if an email is invalid it will store that as null
      $value = (filter_var($value, FILTER_VALIDATE_EMAIL)) ? $value : null;
      if(checkArray('email', $this->attributes) !== $value) {
        $this->attributes['email_verify'] = "x".str_random(59);
      }
      $this->attributes['email'] = $value;
    }

    public function setMobileAttribute($value)
    {
        $value = ($value)?: null;
        if(checkArray('mobile', $this->attributes) !== $value) {
            $this->attributes['mobile_verify'] = "x".str_random(59);
        }
        $this->attributes['mobile'] = $value;
    }

    public function getExtraInfo($id = "")
    {
        if ($id === "") {
            $id = $this->attributes['id'];
        }
        $info  = new UserAdditionalInfo();
        $infos = $info->where('owner', $id)->pluck('value', 'key')->toArray();
        return $infos;
    }
    public function checkArray($key, $array)
    {
        $value = "";
        if (is_array($array)) {
            if (array_key_exists($key, $array)) {
                $value = $array[$key];
            }
        }
        return $value;
    }
    public function twitterLink()
    {
        $html     = "";
        $info     = $this->getExtraInfo();
        $username = $this->checkArray('username', $info);
        if ($username !== "") {
            $html = "<a href='https://twitter.com/" . $username . "' target='_blank'><i class='fa fa-twitter'> </i> Twitter</a>";
        }
        return $html;
    }
    public function name()
    {

        $first_name = $this->first_name;
        $last_name  = $this->last_name;
        $user_name  = $this->user_name;

        if ($first_name && $last_name) {
            $name = $first_name . ' ' . $last_name;
        }
        elseif ($first_name) {
            $name = $first_name;
        }
        elseif ($first_name && $user_name) {
            $name = $first_name;
        }
        else {
            $name = $user_name;
        }
        return $name;
    }
    public function canned()
    {
        return $this->hasMany('App\Model\helpdesk\Agent_panel\Canned', 'user_id');
    }
    public function getFullNameAttribute()
    {
      return $this->name();
    }

    public function getMetaNameAttribute()
    {
      $name = trim($this->first_name .' '. $this->last_name);
      $email = $this->getOriginal('email');
      $mobile = $this->getOriginal('mobile');
      $username = $this->getOriginal('user_name');

      if($name && $email){
        return $name." <$email>";
      }

      if($name && $mobile){
        return $name." <$mobile>";
      }

      if(!$name){
        return $email ? $email : $username;
      }

      return $name;
    }

    public function getFirstNameAttribute($value)
    {
        // $title = "";
        // $array = imap_mime_header_decode($value);
        // if (is_array($array) && count($array) > 0) {
        //     foreach ($array as $text) {
        //         $title .= $text->text;
        //     }
        //     $value = $title;
        // }
        return utfEncoding($value);
    }
    public function getLastNameAttribute($value)
    {
        // $title = "";
        // $array = imap_mime_header_decode($value);
        // if (is_array($array) && count($array) > 0) {
        //     foreach ($array as $text) {
        //         $title .= $text->text;
        //     }
        //     $value = $title;
        // }
        return utfEncoding($value);
    }
    public function notification()
    {
        $related = 'App\Model\helpdesk\Notification\Notification';
        return $this->hasMany($related, 'by');
    }

//    public function save() {
//        dd($this->id);
//        parent::save();
//    }
//    public function save(array $options = array()) {
//        parent::save($options);
//        dd($this->where('id',$this->id)->select('first_name','last_name','user_name','email')->get()->toJson());
//    }

    public function ticketsRequester()
    {
        $related = 'App\Model\helpdesk\Ticket\Tickets';
        return $this->hasMany($related, 'user_id');
    }
    public function ticketsAssigned()
    {
        $related = 'App\Model\helpdesk\Ticket\Tickets';
        return $this->hasMany($related, 'assigned_to');
    }
    public function assignedDepartment()
    {
        $related = 'App\Model\helpdesk\Agent\DepartmentAssignAgents';
        return $this->hasMany($related, 'agent_id');
    }
    public function org()
    {
        return $this->hasMany('App\Model\helpdesk\Agent_panel\User_org', 'user_id');
    }
   
    public function save(array $options = array())
    {
        $changed = $this->isDirty() ? $this->getDirty() : false;
        parent::save();
        $this->updateDeletedUserDependency($changed);
        return true;
    }
    public function updateDeletedUserDependency($changed)
    {
        if ($changed && checkArray('is_delete', $changed) == '1') {
            $this->ticketsAssigned()->whereHas('statuses.type', function($query) {
                $query->where('name', 'open');
            })->update(['assigned_to' => null]);
        }
    }
    public function isDeleted()
    {
        $is_deleted = $this->attributes['is_delete'];
        $check      = false;
        if ($is_deleted) {
            $check = true;
        }

        return $check;
    }
    
    public function isActive()
    {
        $is_deleted = $this->attributes['active'];
        $check      = false;
        if ($is_deleted) {
            $check = true;
        }

        return $check;
    }
    public function isMobileVerified()
    {
        $is_deleted = $this->attributes['mobile_verify'];
        $check      = false;
        if ($is_deleted) {
            $check = true;
        }

        return $check;
    }
    /**
     * @category function to retrun mobile number value
     * @param $value
     * @return $value
     */
    public function getMobileAttribute($value)
    {
        return (string)$value;
    }
    /**
     * @category function to retrun country_code value
     * @param $value
     * @return $value
     */
    public function getCountryCodeAttribute($value)
    {
        if (!is_numeric($this->getMobileAttribute($value))) {
            return '';
        }
        return $value;
    }
    /**
     * @category function to retrun user's full name with email or username to avoid confusion of same name
     * @param $value
     * @return string
     */
    public function getFullNameWithEmailUsernameAttribute($value)
    {
        if ($this->email !== null) {
            return $this->name() . ' &rlm;(' . $this->user_name . ')';
        }
        else {
            return $this->name() . ' &rlm;(' . $this->email . ')';
        }
    }
    public function type()
    {
        return $this->hasMany('App\Model\helpdesk\Agent\AgentTypeRelation', 'agent_id');
    }

    public function managerOfDepartments()
    {
        return $this->belongsToMany('App\Model\helpdesk\Agent\Department', 'department_assign_manager', 'manager_id', 'department_id');
    }

    public function teamLead()
    {
        return $this->hasMany('App\Model\helpdesk\Agent\Teams', 'team_lead');
    }
    public function orgManager()
    {
        return $this->hasMany('App\Model\helpdesk\Agent_panel\User_org', 'user_id');
    }
    public function getHashIdsAttribute($value)
    {
        $hashids = new Hashids('', 10);
        return $hashids->encode($this->id);
    }
    public function thread()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_Thread', 'user_id');
    }
    public function responses()
    {
        return $this->thread()->where('poster', 'support')->where('is_internal', 0)->count();
    }
    public function avgResponseTime()
    {
        return $this->thread()->where('poster', 'support')->where('is_internal', 0)->avg('response_time');
    }
    public function totalResponseTime()
    {
        return $this->thread()->where('poster', 'support')->where('is_internal', 0)->sum('response_time');
    }
    public function timezone(){
        return $this->hasOne('App\Model\kb\Timezone','id','agent_tzone');
    }

    /**
     * To get related data from user_assign_organization table using User_org model
     * @param void
     * @return Querybuilder object
     */
    public function getUsersOrganisations()
    {
        return $this->hasMany(
            'App\Model\helpdesk\Agent_panel\User_org',
            'user_id',
            'id'
        );
    }

    public function organizations()
    {
        try{
            return $this->belongsToMany(
                'App\Model\helpdesk\Agent_panel\Organization',
                'user_assign_organization',
                'user_id',
                'org_id'
            )->withTimestamps();}
            catch(\Exception $e){
                return $e->getMessage();
        }
    }

    /**
     * Checks whether the user is a manager of any single organization, department, organization department
     *  or team based on the value passed as argument
     * @param   string   $argument    tells for which entity we are checking user as manager
     * @var     boolean  $isManager
     * @return  boolean  $isManager   true is user is manager of the entitiy passed as argument, false otherwise
     */
    public function isManagerOf($argument = 'organization')
    {
        $isManager = false;
        switch ($argument == 'organization') {
            case 'value':
                if ($this->orgManager()->where('role', 'manager')->count() > 0) {
                    $isManager = true;
                }
                break;
            // other cases can be added here
            default:

                break;
        }

        return $isManager;
    }


    /**
    * Relation with \App\FaveoReport\Models\Report
    */
    public function reports()
    {
        return $this->hasMany(\App\FaveoReport\Models\ReportDownload::class);
    }

    public function customFieldValues()
    {
        return $this->morphMany('App\Model\helpdesk\Form\CustomFormValue', 'custom');
    }

    public function ticketActionEmails()
    {
        return $this->belongsToMany(\App\Model\helpdesk\Ticket\TicketActionEmail::class);
    }


    /**
     * Relation with ticket filter
     */
    public function ticketFilters()
    {
        return $this->hasMany(\App\Model\helpdesk\Ticket\TicketFilter::class);
    }

    /**
     * Relation with ticket filter share
     */
    public function ticketFilterShares()
    {
        return $this->morphToMany(\App\Model\helpdesk\Ticket\TicketFilter::class, 'ticket_filter_shareable');
    }


    /**
     * relationship to get the list of departments user is in
     */
    public function departments()
    {
        return $this->belongsToMany(
            'App\Model\helpdesk\Agent\Department',
            'department_assign_agents',
            'agent_id',
            'department_id'
        );
    }
    /**
     * relationship to get the list of types user is in
    */
    public function types()
    {
        return $this->belongsToMany(
            'App\Model\helpdesk\Manage\Tickettype',
            'agent_type_relations',
            'agent_id',
            'type_id'
        );
    }
    /**
     * gets the location details for which admin/agent is mapped in
     */
     public function location()
    {
        $related    = 'App\Location\Models\Location';
        $foreignKey = 'location';
        return $this->belongsTo($related, $foreignKey);
    }

    /*
     * Relation with approval workflow
     */
    public function approvalWorkflows()
    {
        return $this->hasMany(\App\Model\helpdesk\Workflow\ApprovalWorkflow::class);
    }

    /**
     * Relation with ticket filter share
     */
    public function approvalLevels()
    {
        return $this->morphToMany(\App\Model\helpdesk\Workflow\ApprovalLevel::class, 'approval_level_approver');
    }

     /**
     * @category function to retrun user timezone if not present will return system timezone
     * @param $value
     * @return string
     */
    public function getAgentTzoneAttribute($value)
    {
        if(!$value){
            return System::value('time_zone_id');
        }
        return $value;

    }

    /**
     * Relation with team
     */
    public function teams()
    {
        return $this->belongsToMany(
            'App\Model\helpdesk\Agent\Teams',
            'team_assign_agent',
            'agent_id',
            'team_id'
        );
    }

    /**
     * Relation with user_notification_tokens
     */
    public function notificationTokens()
    {
        return $this->hasMany('App\Model\Common\UserNotificationToken', 'user_id');
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

//
    public function beforeSave(User $model)
    {   
        $this->validateUser($model);
    }

    /**
     * Validates user attributes
     * @param User $user
     * @throws DuplicateUserException
     * @throws AgentLimitExceededException
     */
    protected function validateUser(User $user)
    {
        // NOTE: not putting unique index at database level, because it will create issues with clients who have this scenario
        $email = $user->email;
        $username = $user->user_name;
        $mobile = $user->mobile;

        if($duplicateUser = $this->getDuplicateUserIfExists($username, $user->id)){
            throw new DuplicateUserException(trans('lang.username_is_already_taken', ['username' => $username, 'name' => $duplicateUser->full_name]));
        }

        if($duplicateUser = $this->getDuplicateUserIfExists($email, $user->id)){
            throw new DuplicateUserException(trans('lang.email_is_already_taken', ['email' => $email, 'name' => $duplicateUser->full_name]));
        }

        if($duplicateUser = $this->getDuplicateUserIfExists($mobile, $user->id)){
            throw new DuplicateUserException(trans('lang.mobile_number_is_already_taken', ['mobile' => $mobile, 'name' => $duplicateUser->full_name]));
        }

        $this->validateForAgentLimit($user);
    }

    /**
     * @param User $user
     * @return bool
     * @throws AgentLimitExceededException
     */
    public function validateForAgentLimit(User $user)
    {
        $allowedAgents = $this->getAgentLimit();


        if($allowedAgents == 0){
            return true;
        }

        // these agents gets created during dummy data installation
        $dummyAgents = self::getDummyAgents();

        // when dummy data is installed
        $alreadyExistingAgentCount = User::where("role", "!=", "user")
            ->where("active", 1)
            ->where(function($q) use($dummyAgents) {
                $q->whereNotIn("email", $dummyAgents->pluck("email")->toArray())
                    ->orWhereNotIn("user_name", $dummyAgents->pluck("user_name")->toArray());
            })
            ->where("id", "!=", $user->id)
            ->count();

        if($user->role && $user->role != "user"){
            ++$alreadyExistingAgentCount;
        }

        if($alreadyExistingAgentCount > $allowedAgents){
            throw new AgentLimitExceededException(trans('lang.user-limit-exceeded-message').helpMessage("plan-upgrade"));
        }
    }

    /**
     * Gets agent limit based on product's license code
     * @return int
     */
    public function getAgentLimit()
    {
        if(!\Schema::hasTable("faveo_license")) {
            return 0;
        }

        $licenseCode = \DB::table('faveo_license')->pluck('LICENSE_CODE')->first();

        return intval(substr($licenseCode, -4));
    }

    /**
     * gets collection of agents which were created during dummy data installation
     * @return \Illuminate\Support\Collection
     */
    public static function getDummyAgents()
    {
        return collect([
            (object)['email'=>'test2@dummydata.com','user_name'=>'batman'],
            (object)['email'=>'test3@dummydata.com','user_name'=>'Helena'],
            (object)['email'=>'test4@dummydata.com','user_name'=>'tony'],
            (object)['email'=>'test5@dummydata.com','user_name'=>'selina'],
            (object)['email'=>'test6@dummydata.com','user_name'=>'Kara'],
            (object)['email'=>'test7@dummydata.com','user_name'=>'demo_agent'],
            (object)['email'=>'test8@dummydata.com','user_name'=>'lois']
        ]);
    }

    /**
     * @param $value
     * @param $userId
     * @return User|null
     */
    private function getDuplicateUserIfExists($value, $userId)
    {
        return User::where("id", "!=", $userId)->where(function ($q) use ($value) {
            $q->where(function ($sq) use ($value) {
                $sq->where("user_name", "!=", null)->where("user_name", "!=", "")->where("user_name", $value);
            })->orWhere(function ($sq2) use ($value){
                $sq2->where("email", "!=", null)->where("email", "!=", "")->where("email", $value);
            })->orWhere(function ($sq3) use ($value){
                $sq3->where("mobile", "!=", null)->where("mobile", "!=", "")->where("mobile", $value);
            });
        })->select("first_name", "last_name", "email", "mobile", "user_name", "email")->first();
    }

    public function todos()
    {
        return $this->hasMany(UserTodo::class);
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = is_numeric($value) ? $value : null;
    }

    public function setWorkPhoneAttribute($value)
    {
        $this->attributes['phone_number'] = is_numeric($value) ? $value : null;
    }

    /**
     * Accessor method for mobile_verified which is a custom appended column for masking value of
     * mobile_verify as mobile_verify contains token and sending that info to response might
     * be dangerous if there is an employee who is a trojan deployed by the rivals.
     */
    public function getMobileVerifiedAttribute()
    {
        return $this->mobile_verify == 1;
    }

    /**
     * Accessor method for email_verified which is a custom appended column for masking value of
     * email_verify as email_verify contains token and sending that info to response might
     * be dangerous if there is an employee who is a trojan deployed by the rivals.
     */
    public function getEmailVerifiedAttribute()
    {
        return $this->email_verify == 1;
    }

    /**
     * Mutator method to set iso value
     * if proper value of iso is passed then it will store that value
     * if iso value is not passed then it will store previous value
     * if new user is created and iso value is not passed, then it will store NULL  for iso
     */
    public function setIsoAttribute($value)
    {
        $this->attributes['iso'] = $value ?: self::whereId($this->id)->value('iso');    
    }

    /** 
     * method to make relationship with UserPermission Model
     */
    public function permissions() {
         return $this->belongsToMany(
            UserPermission::class,
            'user_permission',
            'user_id',
            'permission_id'
        );
    }

    /**
     * method to check whether the agent has particular permission or not based on permission key
     * @param string $key
     * @param int $userId
     * @return bool $pemissionStatus (whether the logged in agent has that particular permission or not)
     */
    public static function has(string $key, int $userId = null)
    {   
        try {
            $permissionStatus = false;
            $user = $userId ? User::find($userId) : Auth::user();
            // previous condition inherited from TicketPolicy class
            if(!$user || $user->role == "user" || $user->role == "admin"){
                return true;
            }
        
            if ($key) {
                $permissionStatus = (bool) $user->permissions()->where('key', $key)->count();
            }

            return $permissionStatus;
        } catch (Exception $exception) {
            // this exception is for developer, more information regarding error
            throw new Exception($exception->getMessage(), 1);
        }
    }
}
