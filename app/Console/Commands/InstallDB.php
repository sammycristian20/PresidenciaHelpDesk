<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Installer\helpdesk\InstallController;
use DB;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;
use Config;

class InstallDB extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'installing database';
    protected $install;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->install = new InstallController();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            $env = base_path() . DIRECTORY_SEPARATOR . '.env';
            if (!is_file($env)) {
                throw new \Exception("Please run 'php artisan install:faveo'");
            }
            if ($this->confirm('Do you want to migrate tables now?')) {
                $dummy_confirm = $this->confirm('Would you like to install dummy data in database to test before going live?');
                $this->call('key:generate', ['--force' => true]);
                $this->checkDBVersion();
                (new SyncFaveoToLatestVersion)->sync();

                if ($dummy_confirm) {
                  $path = base_path().'/DB/dummy-data.sql';
                  DB::unprepared(file_get_contents($path));
                }

                $headers = ['user_name', 'email', 'password'];
                $data = [
                    [
                        'user_name' => 'demo_admin',
                        'email' => '',
                        'password' => 'demopass'
                    ],
                ];
                $env = $this->choice(
                        'Select application environment', ['production', 'development', 'testing']
                );
                $this->install->updateInstalEnv($env);
                $this->table($headers, $data);
                $this->warn('Please update your email and change the password immediately');
                $url = Config::get('app.url');
                $this->info("Faveo has been installed successfully. Please visit $url to login");
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }
    }

    
    /**
     * Function fetches database version from connection and compares it with
     * minimum required verion
     * 
     * @return void
     */
    private function checkDBVersion():void
    {
        try {
            $pdo = DB::connection()->getPdo();
            $version = $pdo->query('select version()')->fetchColumn();
            if(strpos($version, 'Maria') === false) {
                $this->checkMySQLVersion($version);
                return ;
            }
            $this->checkMariaDBVersion($version);
        } catch(\Exception $e) {
            if($e->getCode() != 1049) throw $e;
            $database = config('database.connections.mysql.database');
            config(['database.connections.mysql.database' => null]);
            createDB($database);
            config(['database.connections.mysql.database' => $database]);
            DB::reconnect();
            $this->checkDBVersion();
        }
    }

    /**
     * Function to check version requirement for MariaDB
     * @param  string  $version
     * @return void
     */
    private function checkMariaDBVersion(string $version):void
    {
        $this->compareVersion($this->printAndFormatVersion($version, 'MariaDB'), '10.3', 'MariaDB');        
    }

    /**
     * Function to check version requirement for MySQL
     * @param  string  $version
     * @return void
     */
    private function checkMySQLVersion(string $version):void
    {
        $this->compareVersion($this->printAndFormatVersion($version, 'MySQL'), '5.6', 'MySQL');
    }

    /**
     * Function compares database version with minimum required version
     *
     * @param   string    $version  unfomatted version string
     * @param   string    $min      minimum required version for database
     * @param   string    $db       database name
     * @return  void
     * @throws  Exception
     */
    private function compareVersion($version, $min, $db='MySQL'):void
    {
        if(version_compare($version, $min) < 0) {
            throw new \Exception("Please update your $db database version to $min or greater");
        }
    }

    /**
     * Function prints database version and returns formatted version string
     * 
     * @param   string  $version  unfomatted version string
     * @param   string  $db       database name
     * @return  string            formatted version string
     */
    private function printAndFormatVersion(string $version, string $db = 'MySQL'):string
    {
        $this->info("You are running $db database on version $version");
        preg_match("/^[0-9\.]+/", $version, $match);
        return $match[0];
    }
}
