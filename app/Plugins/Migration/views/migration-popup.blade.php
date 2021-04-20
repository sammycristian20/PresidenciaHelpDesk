<style>
    .drop {
    width: 170px;
    text-align: center;
    padding: 50px 10px;
    margin: auto;
    width:100%;
    min-height: 250px;
}

</style>
<div style="margin-bottom: 9px;">
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal" style="border-radius: 0px;background-color: #f0f0f0;" ng-click="getImageApi()"><i class="fa fa-caret-square-o-right" style="margin-right: 5px"></i>Add Media</button>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="min-width: 70%">

            <!-- Modal content-->
            <div class="modal-content" style="border-radius: 0px">
                <div class="modal-header" style="border-bottom: 0px;padding-bottom: 0px;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Insert Media</h4>
                </div>
                <div class="modal-body" style="padding-bottom: 0px">
                    <div class="row">
                        <div class="col-sm-12" style="padding-left: 0px;padding-right: 0px;">

                               <div id="upload" class="tab-pane fade ">
                                    <p>
                                    <div flow-init="{target:'{{url('chunk/upload')}}',testChunks:false}" flow-files-submitted="$flow.upload()"  flow-file-success="someHandlerMethod( $file, $message, $flow )" flow-file-error="$file.msg = $message" flow-file-added="someHandlerMethod( $file, $event, $flow )">
                                        <table class="table">
                                            <tr ng-repeat="file in $flow.files">
                                                  <td style="color: red;">Error</td>
                                                  <td style="color: red;">@{{file.name}}</td>
                                                  <td style="color: red;">@{{file.msg}}</td>
                                            </tr>
                                        </table>

                                        <div  class="drop" flow-drop="" ng-class="dropClass">
                                            <h3>Drop File Anywhere to Upload</h3>
                                            <p>or</p>
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
                                                        <a class="btn btn-xs btn-warning ng-hide" ng-click="file.pause()" ng-show="!file.paused & amp; & amp; file.isUploading()">
                                                            Pause
                                                        </a>
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
                                    </p>

                                </div>
                                
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info btn-md" ng-disabled="inlineImage" data-dismiss="modal" ng-click="pushImage()">Inline</button>
                    <button type="button" class="btn btn-info btn-md" ng-disabled="disable" data-dismiss="modal" ng-click="pushToEditor()">Attach</button>
                </div>
            </div>

        </div>
    </div>

</div>
