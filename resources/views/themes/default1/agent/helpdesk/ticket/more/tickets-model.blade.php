<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
<div class="modal fade show" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: none; padding-right: 15px;background-color: rgba(0, 0, 0, 0.7);">
    <div class="modal-dialog" role="document">
        
        <div class="col-md-12">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"></h4>
                    <button type="button" class="close closemodal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="custom-alert-loader" style="display:none;">
                    <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <img src="{{assetLink('image','gifloader')}}"><br/><br/><br/>
                    </div><!-- /.merge-loader -->
                </div>
                </div>
                <div class="modal-body" id="custom-alert-body" >
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default no">{{Lang::get('lang.cancel')}}</button>
                    <button type="button" class="btn btn-primary yes" data-dismiss="modal">{{Lang::get('lang.ok')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- merge tickets modal -->
<div class="modal fade" id="MergeTickets">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! Lang::get('lang.merge-ticket') !!} </h4>
                <button type="button" class="close" data-dismiss="modal" id="merge-close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div><!-- /.modal-header-->
            <div class ="modal-body">
                <div class="row">
                    <div class="col-md-5">
                    </div>
                    <div class="col-md-6" id="merge_loader"  style="display:none;">
                        <img src="{{assetLink('image','gifloader')}}"><br/><br/><br/>
                    </div><!-- /.merge-loader -->
                </div>
                <div id="merge_body">
                    <div id="merge-body-alert">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="merge-succ-alert" class="alert alert-success alert-dismissable do-not-slide" style="display:none;" >
                                    <!--<button id="dismiss-merge" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                                    <h5><i class="icon fas fa-check"></i>{!! Lang::get('lang.alert') !!}!</h5>
                                    <span id="message-merge-succ"></span>
                                </div>
                                <div id="merge-err-alert" class="alert alert-danger alert-dismissable" style="display:none;">
                                    <!--<button id="dismiss-merge2" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                                    <h5><i class="icon fas fa-ban"></i>{!! Lang::get('lang.alert') !!}!</h5>
                                    <span id="message-merge-err"></span>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.merge-alert -->
                    <div id="merge-body-form">
                        <div class="row">
                            <div class="col-md-6">
                                {!! Form::open(['id'=>'merge-form','method' => 'PATCH'] )!!}
                                <label>{!! Lang::get('lang.title') !!}</label>
                                <input type="text" name='title' class="form-control" value="" placeholder="Optional" />
                            </div>
                            <div class="col-md-6">
                                <label>{!! Lang::get('lang.select-pparent-ticket') !!}</label>
                                <select class="form-control" id="select-merge-parent"  name='p_id' data-placeholder="{!! Lang::get('lang.select_tickets') !!}" style="width: 100%;"><option value=""></option></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>{!! Lang::get('lang.merge-reason') !!}</label>
                                <textarea  name="reason" class="form-control" height="120px"></textarea>
                            </div>

                        </div>
                    </div><!-- mereg-body-form -->
                </div><!-- merge-body -->
            </div><!-- /.modal-body -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="dismis2">{!! Lang::get('lang.close') !!}</button>
                <input  type="submit" id="merge-btn" class="btn btn-primary" value="{!! Lang::get('lang.merge') !!}"></input>
                {!! Form::close() !!}
            </div><!-- /.modal-footer -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Assign ticket model-->
<div class="modal fade" id="AssignTickets">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! Lang::get('lang.assign-ticket') !!} </h4>
                <button type="button" class="close" data-dismiss="modal" id="assign-close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               
            </div><!-- /.modal-header-->
            <div class ="modal-body">
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-6" id="assign_loader"  style="display:none;">
                        <img src="{{assetLink('image','gifloader')}}"><br/><br/><br/>
                    </div><!-- /.merge-loader -->
                </div>
                <div id="assign_body">
                    <div id="assign-body-alert">
                        <div class="row">
                            <div class="col-md-12">

                                <div id="assign-succ-alert" class="alert alert-success alert-dismissable do-not-slide" style="display:none;">
                                    
                                    <h5><i class="icon fas fa-check"></i> {!! Lang::get('lang.alert') !!}!</h5>
                                     <span id="message-assign-succ"></span>
                                </div>

                                <div id="assign-err-alert" class="alert alert-danger alert-dismissable" style="display:none;">
                                    <!--<button id="dismiss-assign2" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                                    <h5><i class="icon fas fa-ban"></i> {!! Lang::get('lang.alert') !!}!</h5>
                                    <span id="message-assign-err"></span>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.assign-alert -->
                    <div class="row">
                        <div class="col-md-12">
                            <form id="assign-form">
                                {{ csrf_field() }}
                                <label>{!! Lang::get('lang.whome_do_you_want_to_assign_ticket') !!}</label>
                                <select id="assign" class="form-control" name="assign_to" style="width:100%;display: block; max-height: 200px; overflow-y: auto;" multiple="true">

                                
                                </select>

                        </div>
                    </div>
                </div><!-- mereg-body-form -->
            </div><!-- merge-body -->
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="dismis2">

                    <i class="fas fa-times"> </i> {!! Lang::get('lang.close') !!}
                </button>
                <button  type="submit" id="merge-btn" class="btn btn-primary">
                    
                    <i class="fas fa-hand-point-right"> </i> {!! Lang::get('lang.assign') !!}
                </button>
                {!! Form::close() !!}
            </div><!-- /.modal-footer -->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Assign ticket model-->