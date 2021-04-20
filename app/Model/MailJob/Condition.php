<?php

namespace App\Model\MailJob;

use Illuminate\Database\Eloquent\Model;
use App\Model\helpdesk\Settings\Plugin;

class Condition extends Model {

    protected $table = "conditions";
    protected $fillable = ['job', 'value', 'icon', 'command', 'job_info', 'active', 'plugin_job', 'plugin_name'];

    public function getConditionValue($job) {
        $value = ['condition' => '', 'at' => ''];
        $condition = $this->where('job', $job)->first();
        if ($condition) {
            $condition_value = explode(',', $condition->value);
            $value = ['condition' => $condition_value, 'at' => ''];
            if (is_array($condition_value)) {
                $value = ['condition' => $this->checkArray(0, $condition_value), 'at' => $this->checkArray(1, $condition_value)];
            }
        }
        return $value;
    }

    public function setActiveAttribute($value)
    {
      if(!$this->plugin_name){
        $this->attributes['active'] = $value;
      }
      else {
        $isPluginActive = Plugin::where('name', $this->plugin_name)->where('status', 1)->count();
        if($isPluginActive){
          $this->attributes['active'] = $value;
        }else{
          $this->attributes['active'] = 0;
        }
      }
    }

    public function checkArray($key,$array){
        $value = "";
        if(is_array($array)){
            if(array_key_exists($key, $array)){
                $value = $array[$key];
            }
        }
        return $value;

    }

    /**
     * Funtion retuns an array containing list of all jobs which are active
     *
     * @return Array  $result
     */
    public function checkALLActiveJob()
    {    
        return array_merge($this->checkActiveJob(), $this->checkALLPluginActiveJob());
    }

    /**
     * Funtion retuns an array containing list of system's default jobs which are active
     *
     * @return Array  $result
     */
    public function checkActiveJob() {
        $result  = $this->where('active', 1)
        ->whereNull('plugin_name')->orderBy('id')->get(['job', 'command'])->map(function($item){
            return [$item['job'] => $item['command']];
        })->collapse()->toArray();

        return $result;
    }

    /**
     * Funtion retuns an array containing list of all Plugin jobs which are active
     *
     * @return Array  $result
     */
    public function checkALLPluginActiveJob() {
        $plugins = Plugin::where('status',1)->pluck('name')->toArray();
        $result  = $this->where('active', 1)
        ->whereIn('plugin_name', $plugins)->orderBy('id')->get(['job', 'command'])->map(function($item){
            return [$item['job'] => $item['command']];
        })->collapse()->toArray();

        return $result;
    }

    /**
     * Function returns an array containig list of jobs for given plugin passed as paramter
     * @param  String  $pluginName
     *
     * @return Array $result
     */
    public function checkActiveJobsOfPlugin(string $pluginName)
    {
        try{
          $result  = $this->where([
              ['active', '=', 1],
              ['plugin_name', '=', $pluginName]
          ])->get(['job', 'command'])->map(function($item){
              return [$item['job'] => $item['command']];
          })->collapse()->toArray();

          return $result;
        } catch(\Exception $e) {
            loging('jobs', $e->getMessage(), 'error', [$e->getTraceAsString()]);
            return [];
        }
    }
}
