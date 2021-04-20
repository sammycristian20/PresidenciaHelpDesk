<?php


namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App\Http\Controllers\Admin\helpdesk\PHPController as Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use Illuminate\Http\Request;
use Lang;
use App\Model\helpdesk\Settings\Plugin;

class CronSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['roles']);
    }

    /**
     * get the form for cron job setting page.
     *
     * @param type Email    $email
     * @param type Template $template
     * @param type Emails   $email1
     *
     * @return type Response
     */
    public function getSchedular() {
        try{
            $cronPath = base_path('artisan');
            $condition = new \App\Model\MailJob\Condition();
            $execEnabled = $this->execEnabled();
            $paths = $this->getPHPBinPath();
            $jobs = array_merge(
                $condition->whereNull('plugin_name', null)->orderby('id')->get()->toArray(),
                $condition->whereIn('plugin_name', Plugin::where('status',1)->pluck('name')->toArray())->orderby('id')->get()->toArray());
            $commands = [
                ''                   => trans("lang.select"),
                'everyMinute'        => trans("lang.everyMinute"),
                'everyFiveMinutes'   => trans("lang.everyFiveMinutes"),
                'everyTenMinutes'    => trans("lang.everyTenMinutes"),
                'everyThirtyMinutes' => trans("lang.everyThirtyMinutes"),
                'hourly'             => trans("lang.hourly"),
                'daily'              => trans("lang.daily"),
                'dailyAt'            => trans("lang.dailyAt"),
                'weekly'             => trans("lang.weekly"),
                'monthly'            => trans("lang.monthly"),
                'yearly'             => trans("lang.yearly"),
            ];
            return view('themes.default1.admin.helpdesk.settings.cron.cron', compact('commands', 'condition', 'jobs', 'execEnabled', 'cronPath', 'paths'));
        } catch(\Exception $e) {
            
            return redirect('/admin')->with(['fails' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified schedular in storage for cron job.
     * @param  Request  $request
     *
     */
    public function postSchedular(Request $request)
    {
        try {
            $command = new \App\Model\MailJob\Condition();
            $formData = $request->except(['_token', '_method']);
            $command->whereNotIn('active', array_keys($formData))->update(['active' => 0]);
            foreach ($formData as $key => $value) {
                $command->updateOrCreate(
                    ['job' => $key],
                    [
                        'active' => (array_key_exists('active', $value))? (int) $value['active']: 0,
                        'value'  => implode(",", $value['value'])
                    ]);
            }
            /* redirect to Index page with Success Message */
            success: return redirect('job-scheduler')->with(
                'success',
                Lang::get('lang.job_scheduler_saved_successfully'));
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return redirect('job-scheduler')->with('fails', Lang::get('lang.job-scheduler-error') . '<li>' . $e->getMessage() . '</li>');
        }
    }

    public function activateCronUrl(Request $request) {
        $value = 0;
        $status = $request->input('status');
        if ($status == 'true') {
            $value = 1;
        }
        CommonSettings::updateOrCreate(
                ['option_name' => 'cron_url',], ['status' => $value,]
        );
    }
}
