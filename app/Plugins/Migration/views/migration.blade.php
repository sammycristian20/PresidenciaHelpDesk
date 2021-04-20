@extends('themes.default1.admin.layout.admin')

@section('PageHeader')
<h1> {{Lang::get('migration::lang.migration')}} </h1>
@stop
@section('content')
@if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
          
<section ng-controller="migrateCtrl">


<!-- Main content -->
<div ng-show="loadingError">
  <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            @{{error}}
        </div>
</div>
<div ng-show="completeMsg">
  <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
           @{{response}}
        </div>
    </div>
<div class="card card-light">

    <div class="card-header">
        <h3 class="card-title"> {{Lang::get('migration::lang.migrate')}}  </h3>
        <!-- /.box-header -->
        <!-- form start -->
        

    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{Lang::get('migration::lang.application')}}</th>
                            <th>{{Lang::get('migration::lang.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=1; 
                        $apps=['osTicket','Spicework'];
                        ?>
                        @forelse($apps as $app)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$app}}</td>
                            <td ng-show="vj"><a href="javascript:void(0)" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#myModal-8">{{Lang::get('migration::lang.upload')}}</a></td>
                            <td><a href="javascript:void(0)" class="btn btn-sm btn-primary" ng-click="migrate('{{$app}}')" ng-disabled="disabMig"><i class="fas fa-exchange-alt" aria-hidden="true">&nbsp;&nbsp;</i>Migrate</a></td>
                        </tr>
                        <?php $i++;?>
                        @empty 
                        <tr>
                            <td>{{Lang::get('migration::lang.no-application-available-now')}}</td>
                        </tr>
                        @endforelse
                       
                    </tbody>
                    

                </table>
                <div ng-show="loadingSuccess" style="text-align: center;">
                   <img src="{{asset("themes/default/common/images/gifloader.gif")}}">
                   <p>Please wait migrating....@{{completed}} rows completed.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="myModal-8" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal">&times;</button>
                                 <h4 class="modal-title">Upload Here</h4>
                                </div>
                                <div class="modal-body">
                                    <div flow-init="{target:'{{url('migration/upload')}}',testChunks:false}" flow-files-submitted="$flow.upload()"  flow-file-success="someHandlerMethod( $file, $message, $flow )" flow-file-added="someHandlerMethod( $file, $event, $flow )" flow-file-error="$file.msg = $message">
                                        <table class="table">
                                            <tr ng-repeat="file in $flow.files">
                                                  <td style="color: red;">Error</td>
                                                  <td style="color: red;">@{{file.name}}</td>
                                                  <td style="color: red;">@{{file.msg}}</td>
                                            </tr>
                                        </table>
                                        <span class="btn btn-default" flow-btn="" ng-click="check($flow)">Upload File<input type="file" multiple="multiple" style="visibility: hidden; position: absolute;"></span>            
                                        <div class="well" ng-show="$flow.files[0] != null" id="progressHide" style="margin:10px">
                                                <div ng-repeat="file in $flow.files" class="transfer-box ng-scope ng-binding">
                                                    <p>@{{file.name}}</p>
                                                    @{{file.sizeUploaded()}} kB / @{{file.size}} kB | @{{file.sizeUploaded() / file.size * 100 | number:0}}%
                                                    <div class="progress progress-striped" ng-class="{active: file.isUploading()}">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" ng-style="{width: (file.progress() * 100) + '%'}">
                                                        </div>
                                                    </div>
                                                    <div class="btn-group">
                                                        
                                                        <a class="btn btn-xs btn-warning ng-hide" ng-click="file.resume()" ng-show="file.paused">
                                                            Resume
                                                        </a>
                                                        <a class="btn btn-xs btn-danger" ng-click="file.cancel()">
                                                            Cancel
                                                        </a>
                                                        <a class="btn btn-xs btn-info" ng-click="file.retry()" ng-show="file.error">
                                                            Retry
                                                        </a>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                   <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                        </div>
</div>
</section>
@stop
@push('scripts')
<script>
   app.controller('migrateCtrl',function($scope,$compile,$http){
       $scope.migrate=function(app){
           var url="{{url('migration/migrate?app=')}}"+app;
           $scope.loadingSuccess=true;
           $scope.disabMig=true;
           $scope.completed=0;
           $http.get(url).success(function(data){
               if(data.response){
                $scope.url=data.response.url;
                $scope.completed=data.response.completed;
                callAjax($scope.url);
               }
               if(data.message=='success'){
                   $scope.response = 'Migration completed successfully';
                   $scope.loadingSuccess=false;
                   $scope.completeMsg=true;
               }
           })
            .error(function(data){
                $scope.loadingError=true;
                $scope.loadingSuccess=false;
                $scope.error=data.response.message;

            })
       };
       function callAjax(url){
           $http.get(url).success(function(data){
               if(data.response){
                $scope.completed=data.response.completed;
                $scope.url=data.response.url;
                    if($scope.url!=null){
                         callAjax($scope.url);
                   }
               
                }
                if(data.message=='success'){
                   $scope.response = 'Migration completed successfully';
                   $scope.loadingSuccess=false;
                   $scope.completeMsg=true;
               }
           })
           .error(function(data){
                $scope.loadingError=true;
                $scope.loadingSuccess=false;
                $scope.error=data.response.message;
            })
       }
   })
    

</script>
@endpush
