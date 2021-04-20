@extends('themes.default1.admin.layout.admin')

@section('Tickets')
active
@stop

@section('tickets-bar')
active
@stop

@section('bill')
class="active"
@stop

@section('HeadInclude')

<link href="{{assetLink('css','tw-currency-select')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
<link href="{{assetLink('css','bootstrap-select')}}" rel="stylesheet" type="text/css" media="none" onload="this.media='all';">
<style>
   .currency-select ul .currency-select__currency-code{
             font-weight: 400 !important;
        }
        .currency-select .currency-select__flag-currency-code{ padding-left: 15px !important; }
        .currency-select .bs-searchbox{ padding-bottom: 5px !important; }
    .bootstrap-select>.dropdown-toggle{
              background-color: #FFF;
    border-color: #ced4da;
    padding-bottom: 7px;
        }
    .bootstrap-select.btn-group .dropdown-toggle .caret{
             margin-top: 0px;
        }
        .dropdown-menu .inner { display: block; max-height: 150.984px !important; }

    .bootstrap-select .dropdown-menu { max-height: 200px !important; }

</style>
@stop
<!-- header -->
@section('PageHeader')
<h1>{!! trans('lang.tickets') !!}</h1>
@stop
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@stop
<!-- /breadcrumbs -->
@section('content')
<div class="wello" style="display: none"></div>
<div class="card card-light" ng-controller="typeSettingCtrl">

<div class="card-header">
            <h3 class="card-title">{{ Lang::get('lang.edit_bill_settings') }}</h3>
        </div>
<form id="Form">
    <!-- /.box-header -->
    <div class="card-body">
<?php
  if($currency){
$currency=$currency;
  }
  else{
$currency="null";
  }
?>
        
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

        @if (Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fas fa-ban"></i>
            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
        <div>
          <div class="row">
            <div class="col-md-6 {{ $errors->has('status') ? 'has-error' : '' }}">
                    {!! Form::label('status',Lang::get('lang.enable')) !!}
                <div class="row">
                    <?php
                    $yes = false;
                    $no = false;
                    if(isBill()==true){
                        $yes = true;
                    }else{
                        $no = true;
                    }
                    ?>

                    <div class="col-md-3">
                        <p> {!! Form::radio('status',1,$yes) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('status',0,$no) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                </div>             
            </div>
            <div class="col-md-6 {{ $errors->has('show_packages') ? 'has-error' : '' }}">
                    {!! Form::label('status',Lang::get('Bill::lang.show_packages')) !!}
                <div class="row">
                    <?php
                    $yes = false;
                    $no = false;
                    if(commonSettings('bill', 'show_packages')){
                        $yes = true;
                    }else{
                        $no = true;
                    }
                    ?>

                    <div class="col-md-3">
                        <p> {!! Form::radio('show_packages',1,$yes) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('show_packages',0,$no) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                </div>             
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 {{ $errors->has('allowWithoutPackage') ? 'has-error' : '' }}">
                    {!! Form::label('allowWithoutPackage',Lang::get('Bill::lang.allowWithoutPackage')) !!}
                <div class="row">
                    <?php
                      //As value is not being seeded in fresh installation allowWithoutPackage will be null which should be considered as true/1  
                        $yes = (commonSettings('bill', 'allowWithoutPackage')===null)? true : (bool)commonSettings('bill', 'allowWithoutPackage');
                    ?>

                    <div class="col-md-3">
                        <p> {!! Form::radio('allowWithoutPackage',1, $yes) !!} {!! Lang::get('lang.yes') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('allowWithoutPackage',0, !$yes) !!} {!! Lang::get('lang.no') !!}</p>
                    </div>
                </div>             
            </div>
          <div class="col-md-6 {{ $errors->has('trigger_on') ? 'has-error' : '' }} hide" id="trigger" style="display: none;">
                {!! Form::label('trigger_on',Lang::get('lang.trigger-on')) !!}
                {!! Form::select('trigger_on',[''=>'Select','Statuses'=>$statuses],null,['class'=>'form-control']) !!}             
            </div>
            <div class="col-md-6 {{ $errors->has('level') ? 'has-error' : '' }}">
                    {!! Form::label('level',Lang::get('lang.level-of-apply')) !!}
                <div class="row">
                     <?php
                    $curr = "";
                    $thread = ($level&&$level->option_value=='thread')?true:false;
                    $ticket = ($level&&$level->option_value=='ticket')?true:false;
                    $type = ($level&&$level->option_value=='type')?true:false;

                    if($currency!="null" && $currency->option_value){
                        $curr = $currency->option_value;
                    }
                    ?>

                    <div class="col-md-3">
                        <p> {!! Form::radio('level','thread',$thread,['onclick'=>'nontrigger()']) !!} {!!Lang::get('lang.thread')!!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('level','ticket',$ticket,['onclick'=>'trigger()']) !!} {!! Lang::get('lang.ticket') !!}</p>
                    </div>
                    <div class="col-md-3">
                        <p> {!! Form::radio('level','type',$type,['data-toggle'=>'modal','data-target'=>'#typeModal','onclick'=>'typetrigger()','class'=>'billtype']) !!} {!! Lang::get('lang.type') !!}</p>
                    </div>
                </div>             
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 {{ $errors->has('invoice-due') ? 'has-error' : '' }}">
                {!! Form::label('invoice-due',Lang::get('Bill::lang.invoice-due-by')) !!}
                &nbsp;
                {!! Form::input('number', 'invoice-due', (commonSettings('bill', 'invoice-due')) ? commonSettings('bill', 'invoice-due'): 2, ['class' =>'form-control']) !!}            
            </div>
            <div class="col-md-6 {{ $errors->has('currency') ? 'has-error' : '' }}">
                {!! Form::label('currency',Lang::get('lang.currency')) !!}
                &nbsp;
                <currency-select
                class="theDropdown"
                search-placeholder="{!! trans('lang.search') !!}"
                none-selected-text="{!! trans('lang.select') !!}"
                ng-model="selectedCurrencyCode"
                currencies="currencyCodes" style="width: 100%" mapper="codeMapper"
                extractor="codeExtractor"></currency-select>             
            </div>
            </div>
        </div>
  <div class="modal fade" id="typeModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Select Type</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
         <div>
         <div class="well" style="display: none"></div>
          <div class="row">
          <div class="col-sm-5"><label>Type</label></div>
          <div class="col-sm-6"><label>Price</label></div>
          <div class='col-sm-1'></div>
          </div>
          <div class="row" ng-repeat="bill in billingType">
           <div class="col-sm-5">
             <select class="form-control" ng-model='bill.type' ng-focus="getSelectOptions('type',$index)" id="seletom@{{$index}}"  ng-options="opt.optionvalue for opt in bill.option"  style="width:88%;display: inline-block;" ng-change="selectTypeValue(bill.type.optionvalue)">
               <option value="">Select</option>
             </select>
             <span ng-show="loado@{{$index}}" style="width:10%"></span>
           </div>
           <div class="col-sm-6">
               <input type="number" min="0" placeholder="Enter a Price" class="form-control" ng-model='bill.price' onkeypress="return onlyNumbersWithDot(event);">
           </div>
            <div class='col-sm-1'>
              <span ng-show="$last"><a href="javascript:void(0)" ng-click="addmoreType()" title="Add Type" style="font-size: 19px"><i class="fas fa-plus-circle" aria-hidden="true"></i></a></span>
              <span ng-show="!$last"><a href="javascript:void(0)" ng-click="removeType($index)" title="remove Type" style="font-size: 19px;color:#dd4b39"><i class="fas fa-minus-circle" aria-hidden="true"></i></a></span>
            </div>
          </div>
         </div>
         <div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal"><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
        </div>
      
    </div>
  </div>
</div>
<div class="showType" style="display: none">
   <table class="table table-striped" style="border: 1px solid gainsboro;">
    <thead>
      <tr>
        <th style="border-right: 1px solid gainsboro">S.No</th>
        <th style="border-right: 1px solid gainsboro;width: 40%">Type</th>
        <th style="border-right: 1px solid gainsboro">Price</th>
      </tr>
    </thead>
    <tbody>
      <tr ng-repeat="bill in billingType">
        <td style="border-right: 1px solid gainsboro">@{{$index+1}}</td>
        <td style="border-right: 1px solid gainsboro">@{{bill.type.optionvalue}}</td>
        <td style="border-right: 1px solid gainsboro">@{{bill.price}}</td>
      </tr>
    </tbody>
  </table>
  <div style="text-align: right;">
   <button type="button" class="btn btn-primary" data-toggle='modal' data-target='#typeModal'><i class="fas fa-sync">&nbsp;</i>{!!Lang::get('lang.update')!!}</button>
  </div>
</div>
        <!-- /.box-body -->
    </div>
    <div class="card-footer">
         <button type="submit"  class="btn btn-primary"  ng-click="saveBill($event)" ><i class="fas fa-save">&nbsp;</i>{!!Lang::get('lang.save')!!}</button>
    </div>
        </form>
    </div>
    <!-- /.box -->
<script>
  function onlyNumbersWithDot(e) {           
            var charCode;
            if (e.keyCode > 0) {
                charCode = e.which || e.keyCode;
            }
            else if (typeof (e.charCode) != "undefined") {
                charCode = e.which || e.keyCode;
            }
            if (charCode == 46)
                return true
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    function trigger() {
        $("#trigger").show();
        $('.showType').css('display','none');
    }
    function nontrigger() {
        $("#trigger").hide();
        $('.showType').css('display','none');
    }
    function typetrigger(){
       $('.showType').css('display','block');
    }
</script>
@stop


@section('FooterInclude')

<script src="{{assetLink('js','bootstrap-select')}}" type="text/javascript" ></script>
<script type="text/javascript">
   app.directive('input', [function() {
    return {
        restrict: 'E',
        require: '?ngModel',
        link: function(scope, element, attrs, ngModel) {
            if (
                   'undefined' !== typeof attrs.type
                && 'number' === attrs.type
                && ngModel
            ) {
                ngModel.$formatters.push(function(modelValue) {
                    return Number(modelValue);
                });

                ngModel.$parsers.push(function(viewValue) {
                    return Number(viewValue);
                });
            }
        }
    }
}]);
   app.controller('typeSettingCtrl',function($scope,$http){
         $scope.billType={!!$bill_types!!};

         if({!!$currency!!}!=null){
             var billingValues={!!$currency!!};
             $scope.selectedCurrencyCode=billingValues.option_value;
          }
          else{
               $scope.selectedCurrencyCode="";
          }

        if($('.billtype').is(':checked')) {
          $('.showType').css('display','block');
        }
        if($scope.billType.length!=0){
          $.each($scope.billType,function(key,value){
              this['type']={};
              this.type['id']=this.id;
              this.type['optionvalue']=this.optionvalue;
              this['option']=[];
              this.option.push(this.type);
              delete this.id;
              delete this.optionvalue; 
          })
               $scope.billingType=$scope.billType;
        }
        else{
           $scope.billingType=[{'type':'','price':'','option':[]}];
          }
        $scope.typesArray=[];
    // Add type
     $scope.addmoreType=function(){
        $scope.billingType.push({'type':'','price':'','option':[]});
     }
    // Remove type
    $scope.removeType=function(x){
       $scope.billingType.splice(x,1) 
    }

    //currency
    $scope.currencyCodes = ['EUR', 'AUD', 'BGN', 'BRL', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'GBP', 'GEL', 'HKD', 'HUF', 'INR', 'MYR','MXN', 'NOK', 'NZD', 'PLN', 'RON', 'SEK', 'SGD', 'THB', 'NGN', 'PKR', 'TRY', 'USD','ZAR', 'JPY', 'PHP', 'MAD', 'COP', 'AED', 'IDR', 'CLP', 'UAH', 'RUB', 'KRW', 'LKR', 'SAR'];

    $scope.codeMapper = function(code) {
                return {code: code};
            };

            $scope.codeExtractor = function(currency) {
                return currency.code;
            };

     //Get Selected Type Values
        $scope.selectTypeValue=function(x){
            $scope.typesArray.push(x);
        }
    //Get select options
        $scope.bou=0;
          
        $scope.getSelectOptions=function(x,y){
             if($('#seletom'+y).val()!=''){
                $('option:selected','#seletom'+y).remove();
             }
             $scope.bou++;
            var dependancy = x;
            if($scope.bou==1){
            $scope['loado'+y]=true;
            $http.get("{{url('ticket/form/dependancy?dependency=')}}"+dependancy,{params:$scope.typesArray}).success(function (data) {
                 $('#seletom'+y).attr('ng-focus',null).unbind('focus');
                 $('#seletom'+y).attr('ng-click',null).unbind('click');
                 $scope.billingType[y].option=data;
                 //console.log();
                 $('#seletom'+y).css('height', parseInt($('#seletom'+y+' option').length) * 33);
                 //console.log($('#seletom'+y).css('height'));
                 $scope['loado'+y]=false;
                 $scope.bou=0;
            }).error(function(data){
                 $scope['loado'+a]=false;
                 $scope.bou=0;
                 alert("{!!Lang::get('lang.please_click_again')!!}");
            });
            }
           
        }
        $scope.saveBill=function(y){
               // y.currentTarget.innerHTML="<i class='fas fa-circle-notch fa-spin fa-1x fa-fw'> </i> Saving...";
               var serialize=$('#Form').serialize();
               var bilingtype=angular.copy($scope.billingType);
               $.each(bilingtype,function(index, value) {
                    if(typeof this.type=="object"){
                        this['type']=this.type.id;
                    }
                    delete this.option;
               });
               bilingtype = bilingtype.reduce(function (item, e1) { 
                  //console.log(item, e1);
                   var matches = item.filter(function (e2)  
                        { return e1.type == e2.type});
                            if (matches.length == 0) {  
                                     item.push(e1);  
                            }  
                         return item;  
                        }, []);  
               console.log(bilingtype);
             $http.post('{{route("bill.settings.post")}}?'+serialize+'&currency='+$scope.selectedCurrencyCode,bilingtype).success(function(data){
                if(data.success!=undefined&&data.success!=""){
                  $('.wello').css('display','block');      
                  $('.wello').html('<div class="alert alert-success alert-dismissable"><i class="fa  fa-check-circle"></i>&nbsp'+data.success+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>');
                  window.scrollTo(0, 0);
                  setTimeout(function(){
                      $(':input').val('');
                          location.href="{{url('bill')}}";
                   },2000)
                }
             }).error(function(data){
              if(data.error!=undefined){
                    $('.wello').css('display','block');      
                    $('.wello').html('<div class="alert alert-danger alert-dismissable"><i class="fas fa-ban"></i>&nbsp'+data.error+'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>');
                    window.scrollTo(0, 0);
                    y.currentTarget.innerHTML="<i class='fas fa-save'>&nbsp;&nbsp;</i>{!! Lang::get('lang.save') !!}";

              }
              else{
                  var res = "";
                $.each(data, function (idx, topic) {
                   res += "<ul style='list-style-type:none'><li><i class='fas fa-ban'></i>&nbsp" + topic + "</li></ul>";
                });
                $('.wello').html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+res+'</div>');
                $('.wello').css('display','block');  
                y.currentTarget.innerHTML="<i class='fas fa-save'>&nbsp;&nbsp;</i>{!! Lang::get('lang.save') !!}";
              }
                m.currentTarget.disabled=false;
                m.currentTarget.innerHTML = 'Submit';
                $('.wello').css('display','block');      
                window.scrollTo(0, 0);
            })
        } 
  })
</script>
@stop