
@extends('themes.default1.installer.layout.installer')

@section('license')
done
@stop

@section('environment')
done
@stop

@section('database')
active
@stop
<script type="text/javascript"> 
    javascript:window.history.forward(1);
</script> 
@section('content') 

<h1 style="text-align: center;">Database Setup</h1>
This test will check prerequisites required to install Helpdesk<br/>
<?php
/**
 * Faveo HELPDESK Probe
 *
 * Copyright (c) 2014 Ladybird Web Solution.
 *
 */
// -- Please provide valid database connection parameters ------------------------------
$default = Session::get('default');
$host = Session::get('host');
$username = Session::get('username');
$password = Session::get('password');
$databasename = Session::get('databasename');
$dummy_install = Session::get('dummy_data_installation');
$port = Session::get('port');
define('DB_HOST', $host); // Address of your MySQL server (usually localhost)
define('DB_USER', $username); // Username that is used to connect to the server
define('DB_PASS', $password); // User's password
define('DB_NAME', $databasename); // Name of the database you are connecting to
define('DB_PORT', $port); // Name of the database you are connecting to
define('PROBE_VERSION', '4.2');
define('PROBE_FOR', 'HELPDESK 1.0 and Newer');
define('STATUS_OK', 'Ok');
define('STATUS_WARNING', 'Warning');
define('STATUS_ERROR', 'Error');

class TestResult{

    var $message;
    var $status;

    function __construct($message, $status = STATUS_OK) {
        $this->message = $message;
        $this->status = $status;
    }

   

}

    /**
     * Method to check mininmum required version of MySql and MariaDB running on
     * the server.
     * Checking version as an integer value allows us to skip string operations to
     * check if DB is MySQL or MariaDB so we can focus on just to check compatible
     * version of MySQL and MariaDB instead of figuring out what DB server is running.
     * NOTE: This code snippet will work and will not require any modifications until
     *       MySQL releases version 10 which is unlikely to happen in near future. 
     *
     * @param   int  $version  MySQL/MariaDB version as in integer
     * @return  bool           true if $version satisfies minimum requirement else false  
     */
    function compareMySqlAndMariDB(int $version):bool
    {
        /**
         * MySql version less than 5.6 are not compatible so if version is 
         * between 5.6 and 8(including minor and major tags for 8) then we return true
         */
        if($version >= 50600 && $version < 90000) return true;

        /**
         * MariaDB had directly released version 10 after 5.5 so if DB server is MariaDB
         * then we need to check the version must be 10.3 or greater which is compatible
         * with MySQL 5.6. and 5.7
         * @link https://mariadb.com/kb/en/library/mariadb-vs-mysql-compatibility/
         * @link https://en.wikipedia.org/wiki/MariaDB
         */
        if($version >= 100300) return true;

        return false;
    }

// TestResult
if (DB_HOST && DB_USER && DB_NAME) {
    ?>

    <?php
    $mysqli_ok = true;
    $results = array();
    // error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_ALL);
    error_reporting(0);
    try{
    if ($default == 'mysql') {
        
        if(DB_PORT != '' && is_numeric(DB_PORT)) {
            setupConfig($username, $password,$port);
            $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        } else {
            setupConfig($username, $password);
           $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        }
        if ($connection) {
            $results[] = new TestResult('Connected to database as ' . DB_USER . '@' . DB_HOST . DB_PORT, STATUS_OK);
            if (mysqli_select_db($connection, DB_NAME)) {
                $results[] = new TestResult('Database "' . DB_NAME . '" selected', STATUS_OK);
                $mysqli_version = mysqli_get_server_info($connection);
                $dbVersion = mysqli_get_server_version($connection);
                if (compareMySqlAndMariDB($dbVersion)) {
                    $results[] = new TestResult('MySQL version is ' . $mysqli_version, STATUS_OK);
                    // $have_inno = check_have_inno($connection);
                    $sql = "SHOW TABLES FROM " . DB_NAME;
                    $res = mysqli_query($connection, $sql);
                    if (mysqli_fetch_array($res) === null) {
                        $results[] = new TestResult('Database is empty');
                        $mysqli_ok = true;
                    } else {
                        $results[] = new TestResult('Helpdesk installation requires an empty database, your database already has tables and data in it.', STATUS_ERROR);
                        $mysqli_ok = false;
                    }
                } else {
                    $results[] = new TestResult('Your MySQL version is ' . $mysqli_version . '. We recommend upgrading to at least MySQL 5.6 or MariaDB 10.3!', STATUS_ERROR);
                    $mysqli_ok = false;
                } // if
            } else {
               echo '<br><br><p id="fail">Database connection unsuccessful.'.' '.mysqli_connect_error().'</p>';
               $mysqli_ok = false;
            } // if
        } else {
            $dbName = $databasename ;
            createDB($dbName);
            setupConfig($username, $password);
           if(DB_PORT != '' && is_numeric(DB_PORT)) {
            setupConfig($username, $password,$port);
            $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME,DB_PORT);
        } else {
            setupConfig($username, $password);
           $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        }
             // $results[] = new TestResult('Connected to database as ' . DB_USER . '@' . DB_HOST . DB_PORT, STATUS_OK);
            if (mysqli_select_db($connection, DB_NAME)) {
                $results[] = new TestResult('Database "' . DB_NAME . '" selected', STATUS_OK);
                $mysqli_version = mysqli_get_server_info($connection);
                $dbVersion = mysqli_get_server_version($connection);
                if (compareMySqlAndMariDB($dbVersion)) {
                    $results[] = new TestResult('MySQL version is ' . $mysqli_version, STATUS_OK);
                    // $have_inno = check_have_inno($connection);
                    $sql = "SHOW TABLES FROM " . DB_NAME;
                    $res = mysqli_query($connection, $sql);
                    if (mysqli_fetch_array($res) === null) {
                         $dbName = $databasename ;
                         createDB($dbName);
                        $results[] = new TestResult('Database is empty');
                        $mysqli_ok = true;
                    } else {
                       echo '<br><br><p id="fail">Database connection unsuccessful.</p>';
                        $results[] = new TestResult('Helpdesk installation requires an empty database, your database already has tables and data in it.', STATUS_ERROR);
                        $mysqli_ok = false;
                    }
                } else {
                   echo  '<br><br><p id="fail">Database connection unsuccessful.</p>';
                    $results[] = new TestResult('Your MySQL version is ' . $mysqli_version . '. We recommend upgrading to at least MySQL 5.6 or MariaDB 10.3!', STATUS_ERROR);
                    $mysqli_ok = false;
                } // if
            } else {
                echo '<br><br><p id="fail">Database connection unsuccessful.'.' '.mysqli_connect_error().'</p>';
                $mysqli_ok = false;
            } // if
           
        } // if
    }
    }catch(Exception $e) {
        $results[] = new TestResult('Failed to connect to database. ' . $e->getMessage(), STATUS_ERROR);
        $mysqli_ok = false;
    }
    // elseif($default == 'pgsql') {
    //     if ($connection2 = pg_connect("'host='.DB_HOST.' port='.DB_PORT.' dbname='.DB_NAME.' user='.DB_USER.' password='.DB_PASS.")) {
    //         $results[] = new TestResult('Connected to database as ' . DB_USER . '@' . DB_HOST, STATUS_OK);
    //     } else {
    //         $results[] = new TestResult('Failed to connect to database. <br> PgSQL said: ' . mysqli_error(), STATUS_ERROR);
    //         $mysqli_ok = false;
    //     }
    // } elseif($default == 'sqlsrv') {
    // }
    // ---------------------------------------------------
    //  Validators
    // ---------------------------------------------------
// dd($results);
    ?><p class="setup-actions step"><?php
    foreach ($results as $result) {
        print '<br><span class="' . strtolower($result->status) . '">' . $result->status . '</span> &mdash; ' . $result->message . '';
    } // foreach
    ?> </p>
    <!-- </ul> -->
<?php } else { ?>
    <br/>
    <ul>
        <li><p>Unable to test database connection. Please make sure your database server is up and running and PHP is working with session.</p></li>
    </ul>
    <p>If you have fixed all the errors. <a href="{{ URL::route('db-setup') }}">Click here</a> to continue the installation process.</p>
    <?php $mysqli_ok = null; ?>
<?php } ?>

<?php if ($mysqli_ok !== null) { ?>
    <?php if ($mysqli_ok) { ?>

     

        <script src="{{asset("lb-faveo/js/ajax-jquery.min.js")}}"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js"></script>
        <span id="wait"></span>

        {!! Form::open( ['id'=>'form','method' => 'POST'] )!!}
        {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
        <!-- <b>default</b><br> -->
        <input type="hidden" name="default" value="{!! $default !!}"/>
        <!-- <b>Host</b><br> -->
        <input type="hidden"  name="host" value="{!! $host !!}"/>
        <!-- <b>Database Name</b><br> -->
        <input type="hidden" name="databasename" value="{!! $databasename !!}"/>
        <!-- <b>User Name</b><br> -->
        <input type="hidden" name="username" value="{!! $username !!}"/>
        <!-- <b>User Password</b><br> -->
        <input type="hidden" name="password" value="{!! htmlspecialchars($password) !!}"/>
        <!-- <b>Port</b><br> -->
        <input type="hidden" name="port" value="{!! $port !!}"/>
        <!-- Dummy data installation -->
        <input type="hidden" name="dummy_install" value="{!! $dummy_install !!}"/>
        <input type="submit" style="display:none;">

        </form>

        <div id="show" style="display:none;">
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-9" style="text-align: center"id='loader' >
                    <img src="{{assetLink('image','gifloader')}}"><br/><br/><br/>
                </div>
            </div>
        </div>

        <div style="border-bottom: 1px solid #eee;">
          
                 <form action="{{URL::route('getting-started')}}" method="get">
                    <p class="setup-actions step">
                         <input type="hidden" name="timezone" id="tz" value=""/>


                    <input type="submit" value="Continue" id="submitme" class="button-primary button button-large button-next pull-right">

                <a href="{{ URL::route('db-setup') }}" class="button button-large button-next" style="float: left" id="previous">Previous</a>
            </p>
            </form>
           
        </div>

        <br/>
        
        <script type="text/javascript">
        // submit a ticket
        $(document).ready(function () {
             var tz = jstz.determine(); // Determines the time zone of the browser client
                var timezone = tz.name(); //'Asia/Kolkata' for Indian Time.
                $('#tz').val(timezone);
            $("#form").submit();
        });
        // Edit a ticket
        $('#form').on('submit', function () {
            $.ajax({
                type: "GET",
                url: "{!! url('create/env') !!}",
                dataType: "json",
                data: $(this).serialize(),
                beforeSend: function () {
                    $("#conn").hide();
                    $("#previous").hide();
                    $("#show").show();
                    $("#wait").show();
                },
                success: function (response) {
                     var tz = jstz.determine(); // Determines the time zone of the browser client
                    var timezone = tz.name(); //'Asia/Kolkata' for Indian Time.
                    var data=response.result;
                    var message = data.success;
                    var next = data.next;
                    var api = data.api;
                    $('#submitme').attr('disabled','disabled');
                    $('#wait').append('<ul><li>'+message+'</li><li class="seco">'+next+'...</li></ul>');
                    callApi(api);
                },
                error: function(response){
                    var data=response.responseJSON.result;
                    $('#wait').append('<ul><li style="color:red">'+data.error+'</li></ul>');
                    $('#loader').hide();
                    $('#next').find('#submitme').hide();
                    $('#retry').append('<input type="button" id="submitm" class="button-primary button button-large button-next" value="Retry" onclick="reload()">');
                    $("#previous").show();
                    
                }
            })
            return false;
        });

        function callApi(api) {
            $.ajax({
                type: "GET",
                url: api,
                dataType: "json",
                data: $(this).serialize(),
                success: function (response) {
                    var data=response.result;
                    var message = data.success;
                    var next = data.next;
                    var api = data.api;
                    $("#wait").find('.seco').remove();
                    $('#wait ul').append('<li>'+message+'</li><li class="seco">'+next+'...</li>');
                    if (message == 'Database has been setup successfully.') {
                        $('#loader').hide();
                        $('#next').find('#submitme').show();
                        $('#submitme').removeAttr('disabled');
                        $('.seco').hide();
                        $("#previous").show();
                    } else {
                        //show message
                        //show next
                        callApi(api);
                    }
                },
                error: function(response){
                    var data=response.responseJSON.result;
                    $('.seco').append('<p style="color:red">'+data.error+'</p>');
                    $('#loader').hide();
                    $('#next').find('#submitme').hide();
                    $('#retry').append('<input type="button" id="submitm" class="button-primary button button-large button-next" value="Retry" onclick="reload()">');
                    $("#previous").show();
                }
            });
        }
        function reload(){
            $('#retry').find('#submitm').remove();
            $('#loader').show();
            $('#wait').find('ul').remove();
            $.ajax({
                type: "GET",
                url: "{!! url('create/env') !!}",
                dataType: "json",
                data: $('#form').serialize(),
                beforeSend: function () {
                    $("#conn").hide();
                    $("#show").show();
                    $("#wait").show();
                    $("#previous").hide();
                },
                success: function (response) {
                    var data=response.result;
                    var message = data.success;
                    var next = data.next;
                    var api = data.api;
                    $('#submitme').attr('disabled','disabled');
                    $('#wait').append('<ul><li>'+message+'</li><li class="seco">'+next+'...</li></ul>');
                    callApi(api);
                },
                error: function(response){
                    var data=response.responseJSON.result;
                    $('#wait').append('<ul><li style="color:red">'+data.error+'</li></ul>');
                    $('#loader').hide();
                    $('#next').find('#submitme').hide();
                    $('#retry').append('<input type="button" id="submitm" class="button-primary button button-large button-next" value="Retry" onclick="reload()">');
                    $("#previous").show();
                    
                }
            })
            
        }
        </script>

    <?php } else { ?>
      
            
       
        <p>This either means that the username and password information is incorrect or your host is not reachable.</p>
        <ul>
            <li>Are you sure you have a database already existing with the Database name provided?</li>
            <li>Are you sure you have the correct database privileges?</li>
            <li>Are you sure you have the correct username and password?</li>
            <li>Are you sure that you have typed the correct hostname?</li>
            <li>Are you sure that the database server is running?</li>
        </ul>
        <p>If you&rsquo;re unsure what these terms mean you should probably contact your host. If you still need help you can always visit <a href="https://support.faveohelpdesk.com" target="_blank" @if(isWhiteLabelEnabled()) style="pointer-events: none;" @endif>Support</a>.</p>


        <div  style="border-bottom: 1px solid #eee;">
            @if(Cache::has('step3')) <?php Cache::forget('step3') ?> @endif
            <p class="setup-actions step">
                <input type="button" id="submitme" class="button-danger button button-large button-next" style="background-color: #d43f3a;color:#fff;" value="Continue" disabled>
                <a href="{{URL::route('db-setup')}}" class="button button-large button-next" style="float: left;" id="previous">Previous</a>
            </p>
        </div>
        <br/><br/>
    <?php } // if  ?>
    <div id="legend">
        {{-- <ul> --}}
        <p class="setup-actions step">
            <span class="ok">Ok</span> &mdash; All Ok <br/>
            <span class="warning">Warning</span> &mdash; Not a deal breaker, but it's recommended to have this installed for some features to work<br/>
            <span class="error">Error</span> &mdash; HELPDESK require this feature and can't work without it<br/>
        </p>
        {{-- </ul> --}}
    </div>
<?php } // if  ?>

@stop