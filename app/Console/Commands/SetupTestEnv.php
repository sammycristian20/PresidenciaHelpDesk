<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use DB;
use Artisan;
use App\Model\helpdesk\Settings\System;
use App\Plugins\SyncPluginToLatestVersion;
use App\Http\Controllers\Update\SyncFaveoToLatestVersion;


class SetupTestEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing-setup {--username=} {--password=} {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a testing_db, runs migration and seeder for testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dbUsername = $this->option('username') ? $this->option('username') : env('DB_USERNAME');
        $dbPassword = $this->option('password') ? $this->option('password') : env('DB_PASSWORD');
        $dbName = $this->option('database') ? $this->option('database') : 'testing_db';

        $dbPassword = !$dbPassword ? "" : $dbPassword;
        setupConfig($dbUsername, $dbPassword,'','Innodb');

        echo "\nCreating database...\n";

        createDB($dbName);

        echo "\nDatabase Created Successfully!\n";

        //setting up new database name
        Config::set('database.connections.mysql.database', $dbName);

        //setting up app env to testing
        Config::set('app.env', 'testing');
        
        //opening a database connection
        DB::purge('mysql');

        $this->handleFaveoDatabaseOperations();

        $this->handlePluginsDatabaseOperations();

        $this->migrateModules();

        //closing the database connection
        DB::disconnect('mysql');

        $this->createEnv($dbUsername, $dbPassword, $dbName);

        echo "\nTesting Database setup Successfully\n";
    }

    /**
     * migrates DB
     * @return null
     */
    private function handleFaveoDatabaseOperations()
    {
        try{
            echo "\nMigrating and seeding core faveo tables...\n";

            echo (new SyncFaveoToLatestVersion)->sync();

            echo Artisan::output();

            echo "\nMigrated and Seeded core faveo tables Successfully!\n";
        }
        catch(\Exception $e){
            echo "\n".$e->getMessage()."\n";
            /**throwing exception as system got an error during mrigrating
            or seeding core tables */
            throw $e;
        }
    }

    /**
     * Will run plugin migrations
     * @return null
     */
    public function handlePluginsDatabaseOperations()
    {
        $pluginBasePath = app_path().DIRECTORY_SEPARATOR.'Plugins';

        // check all the folders inside plugin folder and run all the migration if exists
        $plugins = scandir($pluginBasePath);

        foreach ($plugins as $plugin)
        {
            $migrationPath = $pluginBasePath.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            $migrationRelativePath = "app/Plugins/$plugin/database/migrations";

            if(file_exists($migrationPath)){
                echo "\n\n\nMigrating and Seeding $plugin tables...";

                //syncing plugins to latest version by running seeders
                echo (new SyncPluginToLatestVersion)->sync($plugin);

                echo "Migrated and Seeded $plugin tables...";
            }
        }
    }

    /**
     * Will run module migrations
     * @return null
     */
    public function migrateModules()
    {
        $moduleBasePath = app_path();

        // check all the folders inside plugin folder and run all the migration if exists
        $modules = scandir($moduleBasePath);

        foreach ($modules as $module)
        {
            $migrationPath = $moduleBasePath.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            $migrationRelativePath = "app/$module/database/migrations";

            if(file_exists($migrationPath)){
                echo "\nMigrating $module tables\n";
                Artisan::call('migrate', ['--path'=>$migrationRelativePath,'--force'=>true]);
                echo Artisan::output();
            }
        }
      }

    

    /**
     * Creates an env file if not exists already
     * @param  string $dbUsername
     * @param  string $dbPassword
     * @return null
     */
    private function createEnv(string $dbUsername, string $dbPassword, string $dbName)
    {
        $env['DB_USERNAME'] = $dbUsername;
        $env['DB_PASSWORD'] = $dbPassword;
        $env['DB_DATABASE'] = $dbName;
        $env['APP_ENV'] = 'development';

        $config = '';

        foreach ($env as $key => $val) {
            $config .= "{$key}={$val}\n";
        }

        $envLocation = base_path() . DIRECTORY_SEPARATOR . '.env.testing';

        // Write environment file
        $fp = fopen(base_path() . DIRECTORY_SEPARATOR . '.env.testing', 'w');
        fwrite($fp, $config);
        fclose($fp);
    }

}
