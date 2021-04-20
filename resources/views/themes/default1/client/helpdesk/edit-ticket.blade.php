<style>
   .btn-bs-file{
    position:relative;
}
.btn-bs-file input[type="file"]{
    position: absolute;
    top: -9999999;
    filter: alpha(opacity=0);
    opacity: 0;
    width:0;
    height:0;
    outline: none;
    cursor: inherit;
}
</style>

@section('content')
<div class="well" style="display:none;"></div>
<div class="box box-primary" ng-controller="CreateTicketAgent">
    <div class="box-header with-border" id='box-header1'>
        <h3 class="box-title">{!! Lang::get('lang.edit_ticket') !!}</h3>
        @if(Session::has('success'))
        <br><br>        
        <div class="alert alert-success alert-dismissable">
            <i class="fa  fa-check-circle"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- failure message -->
        @if(Session::has('fails'))
        <br><br>
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{!! Lang::get('lang.alert') !!}!</b>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
        <div id="response"></div>
        
    </div><!-- /.box-header -->
        <div class="row">
           
        <!-- Nested node template -->

        <script type="text/ng-template" id="nodes_renderer2.html">
          <ng-form name="faveoClientForm">
          <div class="row" style="margin:15px;width:100%" ng-if="node.customerDisplay">
              <div class="col-sm-2" style="padding: 0px;" ng-if="node.title!='Requester'&&node.title!='Description'">
                 <label ng-bind-html="$sce.trustAsHtml(node.clientlabel)"></label><span ng-show="node.customerRequiredFormSubmit==true && node.clientlabel!=''" style="color:red">*</span>
              </div>
              <div class="col-sm-9" style="padding: 0px">
                <input type="text" name="textfield@{{$index}}"  ng-if="node.type=='text'&&node.pattern!=''&&node.pattern!=undefined" class="form-control" style="border-radius: 0px;width:85%" ng-model="node.value" ng-required="@{{node.customerRequiredFormSubmit}}" rpattern="@{{node.pattern}}">
                <span style="color:red" ng-show="faveoClientForm.textfield@{{$index}}.$dirty && faveoClientForm.textfield@{{$index}}.$invalid">
                                          <span ng-show="faveoClientForm.textfield@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                                          <span ng-show="faveoClientForm.textfield@{{$index}}.$error.pattern">@{{node.errmsg}}</span>
                </span>
                <input type="text" name="textfied@{{$index}}"  ng-if="node.type=='text'&&(node.pattern==undefined || node.pattern=='')" class="form-control" style="border-radius: 0px;width:85%" ng-model="node.value" ng-required="@{{node.customerRequiredFormSubmit}}"  >
                <span style="color:red" ng-show="faveoClientForm.textfied@{{$index}}.$dirty && faveoClientForm.textfied@{{$index}}.$invalid">
                                          <span ng-show="faveoClientForm.textfied@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                </span>
                <input type="text"  name="numberfield@{{$index}}"  ng-if="node.type=='number'" class="form-control" style="border-radius: 0px;width:85%" ng-model="node.value"  ng-required="@{{node.customerRequiredFormSubmit}}"  numbers-only>
                <span style="color:red" ng-show="faveoClientForm.numberfield@{{$index}}.$dirty && faveoClientForm.numberfield@{{$index}}.$invalid">
                                          <span ng-show="faveoClientForm.numberfield@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                </span>
                <input type="text"  name="datefield@{{$index}}" ng-if="node.type=='date'" class="form-control" style="border-radius: 0px;width:85%" ng-pattern="/^(0[1-9]|[12][0-9]|3[01])[\/](0[1-9]|1[012])[\/](19|20)\d\d$/" ng-required="@{{node.customerRequiredFormSubmit}}" placeholder="DD/MM/YYYY" ng-model="node.value">
                <span style="color:red" ng-show="faveoClientForm.datefield@{{$index}}.$dirty && faveoClientForm.datefield@{{$index}}.$invalid">
                                          <span ng-show="faveoClientForm.datefield@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                                          <span ng-show="faveoClientForm.datefield@{{$index}}.$error.pattern">Please Enter Valid Correct Format-DD/MM/YYYY</span>
                </span>
                <textarea name="descrip@{{$index}}"  class="form-control" ng-if="node.type=='textarea'&& node.default=='no' && node.pattern != ''" style="border-radius: 0px;width:85%" ng-model="node.value" ng-required="@{{node.customerRequiredFormSubmit}}" rpattern="@{{node.pattern}}" ></textarea>
                <span style="color:red" ng-show="faveoClientForm.descrip@{{$index}}.$dirty && faveoClientForm.descrip@{{$index}}.$invalid">
                                          <span ng-show="faveoClientForm.descrip@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                                          <span ng-show="faveoClientForm.descrip@{{$index}}.$error.pattern">@{{node.errmsg}}</span>
                </span>
                 <textarea name="descript@{{$index}}"  class="form-control" ng-if="node.type=='textarea'&& node.default=='no' && node.pattern == ''" style="border-radius: 0px;width:85%" ng-model="node.value" ng-required="@{{node.customerRequiredFormSubmit}}" ></textarea>
                <span style="color:red" ng-show="faveoClientForm.descript@{{$index}}.$dirty && faveoClientForm.descript@{{$index}}.$invalid">
                                          <span ng-show="faveoClientForm.descript@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                </span>
                <div ng-if="node.type=='select'&&node.default=='yes'">

                <select  ng-model="node.value" name="selected@{{$index}}" id="@{{node.unique}}" ng-options="option.optionvalue for option in node.options" class="form-control" style="border-radius: 0px;width:85%;display:inline-block" ng-required="@{{node.customerRequiredFormSubmit}}" ng-focus="getFocus(node.api,$event,$index)"  ng-disabled="@{{node.disabled}}">
                  <option value="">Select</option>
                </select>
                <span ng-show="loado@{{$index}}" style="width:15%"><img src="{{asset("lb-faveo/media/images/25.gif")}}" style="width:20px;height:20px"></span>
                <div style="color:red" ng-show="faveoClientForm.selected@{{$index}}.$dirty && faveoClientForm.selected@{{$index}}.$invalid">
                    <span ng-show="faveoClientForm.selected@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                  </div>
                </div>
                <div ng-if="node.title=='Api'">

                <select  ng-model="node.value" name="selectedApi@{{$index}}" id="@{{node.unique}}" ng-options="option.optionvalue for option in node.options" class="form-control" style="border-radius: 0px;width:85%;display:inline-block" ng-required="@{{node.customerRequiredFormSubmit}}" >
                  <option value="">Select</option>
                </select>
                <span ng-show="loado@{{$index}}" style="width:15%"><img src="{{asset("lb-faveo/media/images/25.gif")}}" style="width:20px;height:20px"></span>
                <div style="color:red" ng-show="faveoClientForm.selectedApi@{{$index}}.$dirty && faveoClientForm.selectedApi@{{$index}}.$invalid">
                    <span ng-show="faveoClientForm.selectedApi@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                  </div>
                </div>
              <div ng-if="node.title=='Attachments'" style="margin-top:20px">
                   <div id="@{{node.unique}}" ></div>
                        <label class="btn-bs-file btn btn-sm btn-default @{{node.unique}}">
                              <i class="fa fa-plus"></i>&nbspAttach files
                         <input type="file" multiple ng-model="filename" valid-file onchange="angular.element(this).scope().updateList(this,angular.element(this).scope().node.unique)" ng-required="@{{node.customerRequiredFormSubmit}}"/>
                        </label>
                        <p><i>Max File Upload size : {{file_upload_max_size()}}MB and Max Number of Files :{{ini_get('max_file_uploads')}}</i></p>
                        <span style="color:red;display:none;" class="@{{node.unique}}1">
                                          <span>@{{node.clientlabel}} is required.</span>
                        </span>
                </div>
              <div ng-if="node.title=='Help Topic'">
                <select  ng-model="node.value" name="selected@{{$index}}"  ng-options="option.optionvalue for option in node.options" class="form-control" style="border-radius: 0px;width:85%" ng-required="@{{node.customerRequiredFormSubmit}}" ng-change="nestSelectRTL(node.value,node,'department')" ng-disabled="node.disable">
                  <option value="">Select</option>
                </select>
                <span style="color:red" ng-show="faveoClientForm.selected@{{$index}}.$dirty && faveoClientForm.selected@{{$index}}.$invalid">
                  <span ng-show="faveoClientForm.selected@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
               </span>
              </div>
              <div ng-if="node.title=='Department'">
                <select  ng-model="node.value" name="selected@{{$index}}"  ng-options="option.optionvalue for option in node.options" class="form-control" style="border-radius: 0px;width:85%" ng-required="@{{node.customerRequiredFormSubmit}}" ng-change="nestSelectRTL(node.value,node,'helptopic')" ng-disabled="node.disable">
                  <option value="">Select</option>
                </select>
                <span style="color:red" ng-show="faveoClientForm.selected@{{$index}}.$dirty && faveoClientForm.selected@{{$index}}.$invalid">
                  <span ng-show="faveoClientForm.selected@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
               </span>
              </div>
                <div ng-if="node.type=='select'&&node.default=='no'&& node.title!='Api'">
                <select  ng-model="node.value"    name="selected@{{$index}}"  ng-options="option.optionvalue for option in node.options" class="form-control" style="border-radius: 0px;width:85%" ng-required="@{{node.customerRequiredFormSubmit}}" ng-change="nestSelectRTL()">
                  <option value="">Select</option>
                </select>
                  <span style="color:red" ng-show="faveoClientForm.selected@{{$index}}.$dirty && faveoClientForm.selected@{{$index}}.$invalid">
                    <span ng-show="faveoClientForm.selected@{{$index}}.$error.required">@{{node.clientlabel}} is required.</span>
                  </span>
                </div>
                <ul class="list-group" ng-if="node.type=='radio'" style="border:none">
                      <li ng-repeat="option in node.options"  class="list-group-item" style="border:none">
                                          <label for="happy@{{$index}}" class="radio-inline">
                                          <input type="radio" name="selection@{{node.unique}}" id="happy@{{$index}}" ng-model="node.value" ng-value="option.optionvalue" ng-required="@{{node.customerRequiredFormSubmit}}" ng-click="nestedRadioRTL()" />
                                            @{{option.optionvalue}}</label>
                      </li>
                      <div ng-if="node.clientlabel=='' && node.customerRequiredFormSubmit==true" style="color: red">(*Fields are Required)</div>
                </ul>
                <ul class="list-group" ng-if="node.type=='checkbox'" style="border:none">
                      <div ng-repeat="option in node.options"  class="list-group-item" style="border:none">
                                          <label class="checkbox-inline">
                                           <input type="checkbox" 
                                                name="selectedValue[]"  ng-model="option.checked"   value="@{{option.optionvalue}}" ng-click="updateSelection($parent.$index,option.optionvalue)" ng-required="checkBoxArray@{{$parent.$index}}.length==0" >
                                           @{{option.optionvalue}}</label>
                          <ul ng-model="option.nodes" ng-class="{hidden: collapsed}"  style="list-style-type:none;margin-left: -70px" ng-if="option.checked==true && option.nodes.length>0" class='rtlCheck'>
                              <li  ng-repeat="node in option.nodes" ng-include="'nodes_renderer2.html'"></li>
                       </ul>
                      </div>
                      <div ng-if="node.clientlabel=='' && node.customerRequiredFormSubmit==true" style="color: red">(*Fields are Required)</div>
                </ul>
              </div>
              <div class="col-sm-12"  ng-repeat="option in node.options" ng-if="option.nodes.length>0 && node.value && (node.title=='Nested Select'||node.type=='multiselect')">
                  <ul ng-model="option.nodes" ng-class="{hidden: collapsed}" style="list-style-type:none;margin-left: -70px" ng-if="option==node.value" class="rtlSelect">
                      <li  ng-repeat="node in option.nodes" ng-include="'nodes_renderer2.html'">
                    </li>
                  </ul>

              </div>
              <div class="col-sm-12"  ng-repeat="option in node.options" ng-if="option.nodes.length>0 && node.value && node.title=='Nested Radio'">
                  <ul ng-model="option.nodes" ng-class="{hidden: collapsed}" style="list-style-type:none;margin-left: -70px" ng-if="option.optionvalue==node.value" class="rtlRadio">
                      <li  ng-repeat="node in option.nodes" ng-include="'nodes_renderer2.html'">
                    </li>
                  </ul>
              </div>
             
          </div>
          <ul  ng-model="node.nodes" ng-class="{hidden: collapsed}" style="list-style-type:none">
            <li ng-repeat="node in node.nodes"  ng-include="'nodes_renderer2.html'">
            </li>
          </ul>
          </ng-form>
        </script>
        <div class="col-sm-12" >
          <form name="faveoForm">
            <div  style="margin-right:0px">
                <div class="col-sm-12">
                    <ul  ng-model="tree3"  style="list-style-type:none">
                        <li ng-repeat="node in tree3"  ng-include="'nodes_renderer2.html'">
                            
                        </li>
                    </ul>
                </div>
                <div style="text-align:center"  id="loader">
                        <img src="{{asset("lb-faveo/media/images/gifloader.gif")}}" >
                </div>
            
                <div class="col-sm-12" style="border-top:1px solid gainsboro;background-color: white;padding:5px;text-align: right">
                    <button type="button" class="btn btn-primary" id="submitForm"  data-ng-click="getEditor($event,faveoForm)">Submit</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <?php
    $check_linking_status = \App\Model\helpdesk\Settings\CommonSettings::where('option_name', '=', 'helptopic_link_with_type')->select('status')->first();
    ?>
        <input type="hidden" name="$check_linking_status" value="{{$check_linking_status->status}}" id="check_helptopic_status">
</div><!-- /. box -->
@stop
@push('scripts')
<script src="{{asset('vendor/unisharp/laravel-ckeditor/ckeditor.js')}}"></script>
<script src="{{asset("lb-faveo/js/intlTelInput.min.js")}}"></script>
<script>
  $(document).click(function(e) {
       $('.menu2').hide();
  })
  $(document).ready(function() {
    $(".numberOnly").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl/cmd+A
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: Ctrl/cmd+C
            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: Ctrl/cmd+X
            (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});
</script>
<script>
app.directive("rpattern", function() {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, el, attrs, ngModel) {
            var validator, patternValidator,
                pattern = attrs.rpattern,
                required = true;
            
            if( pattern ) {
                if (pattern.match(/^\/(.*)\/$/)) {
                    pattern = new RegExp(pattern.substr(1, pattern.length - 2));
                    function patternValidator(value) {
                        return validate(pattern, value)
                    };
                }
                else {
                    function patternValidator (value) {
                        var patternObj = scope.$eval(pattern);
                        if (!patternObj || !patternObj.test) {
                            throw new Error('Expected ' + pattern + ' to be a RegExp but was ' + patternObj);
                        }
                        return validate(patternObj, value);
                    };
                }
            }
            
            ngModel.$formatters.push(patternValidator);
            ngModel.$parsers.push(patternValidator);
            
            /*attrs.$observe("required", function(newval) {
              
                required = newval;
                //patternValidator(ngModel.$viewValue);
            });*/
            
            function validate(regexp, value) {
                if( value == null || value === "" || !required || regexp.test(value) ) {
                    ngModel.$setValidity('pattern', true);
                    $('#submitForm').removeAttr('disabled');
                    return value;
                }
                else {
                    ngModel.$setValidity('pattern', false);
                    $('#submitForm').attr('disabled','disabled');
                    return;
                }
            }
        }
    };
});
app.directive('validFile',function(){
  return {
    require:'ngModel',
    link:function(scope,el,attrs,ngModel){
      //change event is fired when file is selected
      el.bind('change',function(){
        scope.$apply(function(){
          ngModel.$setViewValue(el.val());
          ngModel.$render();
          el[0].value="";
        });
      });
    }
  }
});
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
app.controller('CreateTicketAgent', function($scope,$http, $sce,$window,$compile,Upload,$rootScope,$location,vcRecaptchaService){
     $scope.editTicket={!!$ticket!!};
     console.log($scope.editTicket);
     $scope.$sce=$sce;
    $rootScope.disable=true;
      $rootScope.inlineImage=true;
      $rootScope.arrayImage=[];
      $scope.attachmentImage=[];
      $scope.inlinImage=[];
      $scope.editorValues={};
         $scope.tree3 = [];
        $scope.uploadArray=[];
        $scope.multiAttach=[];
        $scope.value=[];
        $scope.UserInfo=[];
        $scope.linkLabel="";
        $(function(){
          $scope.typelink=$('#check_helptopic_status').val();
          //console.log($scope.typelink);
        })
        $scope.emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        $http.get("{{url('form/ticket')}}").success(function (data) {
           $http.get("{{url('ticket/form/dependancy?dependency=helptopic')}}").success(function (data1) {
           for(var i in data[0]){
              if(data[0][i].api=="helptopic"){
                  if(!data[0][i].customerDisplay){
                       data[0][i].link=false;
                    }
                  data[0][i].options=data1; 
                  $http.get("{{url('ticket/form/dependancy?dependency=department')}}").success( function (data2) {
                        for(var i in data[0]){
                            if(data[0][i].api=="department"){
                                 data[0][i].options=data2;   
                              }
                          }
                         let cont=0
                          for(var k in data[0]){
                             if((data[0][k].api=="helptopic"||data[0][k].api=="department")&&data[0][k].link&&data[0][k].customerDisplay){
                                cont++;
                                if(cont=="1"){
                                  data[0][k].customerDisplay=true;
                                  $scope.firstLink=data[0][k].api;
                                }
                                else{
                                  $scope.linkLabel=data[0][k].api;
                                }
                             }
                          }
                          $scope.tree3 = data[0];
                          console.log($scope.tree3);
            
             
        function maker(key, value) {
                 var lang='{{Lang::getLocale()}}';
            if(typeof value==='object'){
                 if(value.type==='checkbox'){
                      $scope['checkBoxArray'+key]=[];
                  }
                  if(value.title=='Attachments'){
                     $scope.UserInfo[value.unique]=[]; 
                  }
                  if(value.title=='Status'){
                    value.customerDisplay=false;
                      value.customerRequiredFormSubmit=false;
                  }
                  if(value.title=="Company"||value.title=="Organisation Department"){
                      value.customerDisplay=false;
                      value.customerRequiredFormSubmit=false;
                      
                  }
                  if(value.title=='Captcha'&&value.customerDisplay){
                        $scope.clientCaptcha=true;
                  }
                  else{
                        vcRecaptchaService.getResponse=function(){
                            return null;
                        };
                  }
                };
                if(typeof value==='object'){
                if((value.type==='radio'||value.type==='checkbox'||value.type==='select' && value.default==='no')||(value.type==='multiselect' && value.default==='yes')){
                    
                    for(var i in value.options){
                      for(var j in value.options[i].optionvalue){
                          if(value.options[i].optionvalue[j].language===lang){
                              value.options[i].optionvalue=value.options[i].optionvalue[j].option;
                              break;
                          }
                      }
                    }
                }
            }
            if(typeof value==='object'){
                if((value.type==='radio'||value.type==='checkbox'||value.type==='select' && value.default==='no')||(value.type==='multiselect' && value.default==='yes')){
                    
                    for(var i in value.options){
                      if(typeof value.options[i].optionvalue ==='object'){
                        for(var j in value.options[i].optionvalue){
                          if(value.options[i].optionvalue[j].language!==lang){
                            if(j==0){
                              value.options[i].optionvalue=value.options[i].optionvalue[j].option;
                            }
                          }
                      }
                     }
                    }
                }
            }
            if(typeof value==='object'){
               if(value.clientlabel!=""){
                for(var a in value.clientlabel){
                  if(value.clientlabel[a].language=='{{Lang::getLocale()}}'){
                     value.clientlabel=value.clientlabel[a].label;
                     break;
                  }
                }
              }
            }
            if(typeof value==='object'){
              if(value.agentlabel!=""){
                for(var a in value.clientlabel){
                  if(typeof value.clientlabel ==='object'){
                    if(value.clientlabel[a].language !=='{{Lang::getLocale()}}'){
                       if(a==0){
                        value.clientlabel=value.clientlabel[a].label;
                      }
                    }
                  }
                }
              }
            }
            if(typeof value==='object'){
                for(var a in value.errmsg){
                  if(value.errmsg[a].language=='{{Lang::getLocale()}}'){
                     value.errmsg=value.errmsg[a].label;
                     break;
                  }
                }
            }
            if(typeof value==='object'){
                for(var a in value.errmsg){
                  if(typeof value.errmsg ==='object'){
                    if(value.errmsg[a].language !=='{{Lang::getLocale()}}'){
                       if(a==0){
                        value.errmsg=value.clientlabel[a].label;
                      }
                    }
                  }
                }
            }
            if (value !== null && typeof value === "object") {
                    $.each(value, maker);
                }
            }
            $.each($scope.tree3, maker);
          //  URLparams binding
          if($scope.editTicket.length!==0){
            for(var i in $scope.editTicket){
              
                
                 
                 function weker(key, value) {
                      if(value !== null && typeof value === "object"){
                         $.each(value, weker);
                      }
                      if (value !== null && typeof value === "object" && value.type!='checkbox' && value.type!='select'&&value.type!='multiselect' && value.default =='no' && value.title!='Subject') {
                           if(i=="formdata"){
                            if($scope.editTicket[i]!=null){
                            for(var m in $scope.editTicket[i]){ 
                              if($scope.editTicket[i][m].key==value.unique){
                                 
                                  value.value=$scope.editTicket[i][m].content;
                              }
                            }
                          }
                        }
                          $.each(value, weker);
                     }
                     else if (value !== null && typeof value === "object" && value.title=='Subject') {
                        if(i=="thread"){
                           if($scope.editTicket[i]!=null){
                            value.value=$scope.editTicket[i][0].subject;
                           }
                          }
                     }
                     else if (value !== null && typeof value === "object" && value.type=='checkbox') {
                          if(i=="formdata"){
                            if($scope.editTicket[i]!=null){
                            for(var m in $scope.editTicket[i]){ 
                              if($scope.editTicket[i][m].key==value.unique){
                                   for(var j in value.options){
                                         var checkValue=$scope.editTicket[i][m].content.split(',');
                                      for(var u in checkValue){
                                        if(value.options[j].optionvalue==checkValue[u]){
                                              value.options[j].checked=true;
                                          }
                                        }
                                        }
                                      }
                                    }
                          }
                        }
                          $.each(value, weker);
                     }
                    else if (value !== null && typeof value === "object" && value.type=="multiselect" && value.default=="yes") {
                            if(i=="helptopic"){
                              if($scope.editTicket[i]!=null){
                                   for(var j in value.options){
                                        if(value.options[j].optionvalue==$scope.editTicket[i].optionvalue){
                                              if(value.options[j].id!=undefined){
                                              value.value=value.options[j];
                                            }
                                            else{
                                              if(value.api=="helptopic"){
                                                  this.clientlabel="";
                                                  this.customerDisplay=false;
                                                }
                                            }
                                          }
                                        }
                                      }
                             } 
                            if(i=='dept_id'){
                                 if($scope.editTicket[i]!=null){
                                      for(var j in value.options){
                                        if(value.options[j].id==$scope.editTicket[i]){
                                            value.value=value.options[j];
                                        }
                                    } 
                                 }
                            }    
                    }
                    
                    else if (value !== null && typeof value === "object" &&value.type=="select" && value.default=="no") {
                          if(i=="formdata"){
                             if($scope.editTicket[i]!=null){
                             for(var m in $scope.editTicket[i]){ 
                              if($scope.editTicket[i][m].key==value.unique){
                                   for(var j in value.options){
                                        if(value.options[j].optionvalue==$scope.editTicket[i][m].content){
                                              value.value=value.options[j];
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                          $.each(value, weker);
                    }
                 }
                 $.each($scope.tree3, weker);
               }
            }
          setTimeout(function() {
          if($scope.editTicket.length!==0){
            for(var i in $scope.editTicket){
              
                
                 function waker(key, value) {
                       if (value !== null && typeof value === "object" && value.title=="Location") {
                         //alert(0);
                        if(i=="formdata"){
                          if($scope.editTicket[i]!=null){
                          for(var m in $scope.editTicket[i]){ 
                              if($scope.editTicket[i][m].key==value.unique){
                                  $('#'+value.unique).html('<option value="'+$scope.editTicket[i][m].content+'" selected="selected">'+$scope.editTicket[i][m].content+'</option>')
                                      //value.value=$scope.editTicket[i][m];    
                                      }
                                    }
                          }
                        }
                     }
                     
                    else if (value !== null && typeof value === "object" &&value.type=="select" && value.title!="Location" && value.default=="yes" && value.customerDisplay) {
                            
                              if(i==value.unique){
                                  if($scope.editTicket[i]!=null){
                                   $('#'+value.unique).html('<option value="'+$scope.editTicket[i].id+'" selected="selected">'+$scope.editTicket[i].optionvalue+'</option>')
                                 }
                                   //value.options=$scope.editTicket[i];
                              }
                              else if(i=="types"){
                                  if($scope.editTicket[i]!=null){
                                   $('#type').html('<option value="'+$scope.editTicket[i].id+'" selected="selected">'+$scope.editTicket[i].optionvalue+'</option>')
                                 }
                                   //value.options=$scope.editTicket[i];
                              }
                              else if(i=="statuses"){
                                  if($scope.editTicket[i]!=null){
                                   $('#status').html('<option value="'+$scope.editTicket[i].id+'" selected="selected">'+$scope.editTicket[i].optionvalue+'</option>')
                                 }
                                   //value.options=$scope.editTicket[i];
                              }
                    }
                    
                 }
                 $.each($scope.tree3, waker);
               }
            }
             },100)
          setTimeout(function(){
                 if('{{Lang::getLocale()}}'=='ar'){
                  $('.input-group').find('.form-control').css('float','inherit');
                  $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float','right');
                  $('.list-group').css('padding-right','0px');
                }
               },2000);
           console.log($scope.tree3)
    
           if('{{Lang::getLocale()}}'=='ar'){
            $('#rtl').css('direction','rtl');
            $('.input-group').find('.form-control').css('float','inherit');
              $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float','right');
              $('.list-group').css('padding-right','0px');
              setTimeout(function(){
                 $('.cke_ltr').attr('dir','rtl');
                 $('.cke_ltr').toggleClass('cke_rtl');
                 $('.cke_wysiwyg_frame').contents().find("html").attr('dir','rtl');
              },500);
            }
           setTimeout(function(){
                       $("#loader").hide();
                    },100);
                 }).error(function(data){
            alert("{!!Lang::get('lang.please_reload_the_page')!!}")
        });  
              }
           }
        }).error(function(data){
            alert("{!!Lang::get('lang.please_reload_the_page')!!}")
        });
//         
        }).error(function(data){
            alert("{!!Lang::get('lang.please_reload_the_page')!!}")
        });
 
 
   $scope.multiselect=function(x){
       var dependancy=$( "#"+x+" option:selected" ).text();
       //alert(dependancy);
      
                 function broker(key,value){
                     if(typeof value=="object" && value.type=="multiselect"){
                         if(value.unique==x){
                               $http.get("{{url('ticket/form/dependancy?dependency=')}}"+dependancy).success(function (data) {
                                        if(data!=null){
                                           
                                        }
                                  });
                         }
                         $.each(value,broker);
                     }
                 }
                  $.each($scope.tree3,broker);
       /*var dependancy=$scope.tree3[x].value.optionvalue;
            */
   }
       $scope.uploadArray = [];
        $scope.uploaded=0;
        $scope.updateList = function (e,x) {
            if($('.'+x+'1').css('display')=='block'){
                $('.'+x+'1').css('display','none');
            }
            $scope.$apply(function () {
                $scope.uploaded++;
                for (var i = 0; i < e.files.length; i++) {
                    if(e.files[i].size < 2048*1024){
                          e.files[i]['id'] = $scope.uploaded + '-' + i;
                          $scope.UserInfo[x].push(e.files[i]);
                    }
                    else{
                        $('#'+x).find('p').remove();
                        setTimeout(function(){
                           $('#'+x).append('<p style="color:red;">cannot upload files more than 2 MB</p>');
                        },100);
                    }
                }
            });
            if ($scope.uploaded == 1) {
               $('#'+x).find('p').remove();
                var output = document.getElementById(x);
                for (var i = 0; i < $scope.UserInfo[x].length; ++i) {
                    $compile($(output).append('<div id="hidden" style="list-style-type:none;background-color: #f5f5f5;border: 1px solid #dcdcdc;font-weight: bold;margin:8px;overflow-y: hidden;padding: 4px 4px 4px 8px;max-width: 448px;margin-left: 0px;" contenteditable="false">' + $scope.UserInfo[x][i].name + '(' + $scope.UserInfo[x][i].size + ')<div style="display:none">' + $scope.UserInfo[x][i].id + '</div><i class="fa fa-times" aria-hidden="true" style="float:right;cursor: pointer;" ng-click="removeAttache($event,\''+x+'\')"></i></div>'))($scope);
                   }
            }
            else {
                $('#'+x).find('p').remove();
                var output = document.getElementById(x);
                output.innerHTML = "";
                for (var i = 0; i < $scope.UserInfo[x].length; ++i) {
                    $compile($(output).append('<div id="hidden" style="list-style-type:none;background-color: #f5f5f5;border: 1px solid #dcdcdc;font-weight: bold;margin:8px;overflow-y: hidden;padding: 4px 4px 4px 8px;max-width: 448px;margin-left: 0px;" contenteditable="false">' + $scope.UserInfo[x][i].name + '(' + $scope.UserInfo[x][i].size + ')<div style="display:none">' + $scope.UserInfo[x][i].id + '</div><i class="fa fa-times" aria-hidden="true" style="float:right;cursor: pointer;" ng-click="removeAttache($event,\''+x+'\')"></i></div>'))($scope);
                }
            }
            console.log($scope.UserInfo);
        }
    $scope.removeAttache = function (x,y) {
            var id = x.currentTarget.parentNode;
            id.remove();
            var value = id.firstElementChild.innerHTML;
            //console.log(value);
            $scope.UserInfo[y] = $.grep($scope.UserInfo[y], function (e) {
                return e.id != value;
      
            })
            if($scope.UserInfo[y].length==0){
                if($('.'+y).find('input').prop('required')){
                   $('.'+y+'1').css('display','block');
                }
            }
        }
    $scope.nestSelectRTL=function(x,y,z){
      
            for(var i in $scope.tree3){
              if($scope.tree3[i].link&&$scope.linkLabel==$scope.tree3[i].api&&x==null&&$scope.firstLink==y.api){
                              $scope.tree3[i].customerDisplay=false;
                              $scope.tree3[i].value="";
              }
           };
            if(y.api=='helptopic'&&$scope.typelink=="1"){
              if(x!=undefined){
                 $http.get("{{ route('search.ticket-type.autofill') }}?ticket_create=1&helptopic="+x.id).success(function (data) {
                      for(var w in $scope.tree3){
                        if($scope.tree3[w].api=='type'){
                           $scope.tree3[w].options=data; 
                        }
                      }
                 })
               }
               else{
                    for(var w in $scope.tree3){
                        if($scope.tree3[w].api=='type'){
                           $scope.tree3[w].options=[]; 
                        }
                    }
               }
            }
            if('{{Lang::getLocale()}}'=='ar'){
              setTimeout(function(){
                 $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float','right');
                 $(".rtlSelect").css({"margin-left":"0px","margin-right":"-70px"})
                },100);
            }
        if(typeof x=="object"&&x!=undefined&&y.api!=$scope.linkLabel){  
           $http.get("{{url('ticket/form/dependancy')}}?dependency="+z+"&linkedtopic="+x.optionvalue).success( function (data) {
            for(var i in data){
                                 for(var j in data[i].optionvalue){
                                      if(data[i].optionvalue[j].language==='{{Lang::getLocale()}}'){
                                          data[i].optionvalue=data[i].optionvalue[j].option;
                                          break;
                                        }
                                 }
                    }
            for(var i in data){
                if(typeof data[i].optionvalue=="object"){
                                 for(var j in data[i].optionvalue){
                                    if(data[i].optionvalue[j].language !=='{{Lang::getLocale()}}'){
                                           if(j==0){
                                              data[i].optionvalue=data[i].optionvalue[j].option;
                                              break;
                                            }
                                    }
                            }
                    }
            }
    for(var g in data){
      if(data[g].nodes!=undefined||data[g].nodes.length!=0){
        
        function worker(key, value) {
                 var lang='{{Lang::getLocale()}}';
            if(typeof value==='object'){
                 if(value.type==='checkbox'){
                      $scope['checkBoxArray'+key]=[];
                  }
                  if(value.title=='Attachments'){
                     $scope.UserInfo[value.unique]=[]; 
                  }

                  
                };
                if(typeof value==='object'){
                if((value.type==='radio'||value.type==='checkbox'||value.type==='select' && value.default==='no')||(value.type==='multiselect' && value.default==='yes')){
                    
                    for(var i in value.options){
                      for(var j in value.options[i].optionvalue){
                          if(value.options[i].optionvalue[j].language===lang){
                              value.options[i].optionvalue=value.options[i].optionvalue[j].option;
                              break;
                          }
                      }
                    }
                }
            }
            if(typeof value==='object'){
                if((value.type==='radio'||value.type==='checkbox'||value.type==='select' && value.default==='no')||(value.type==='multiselect' && value.default==='yes')){
                    
                    for(var i in value.options){
                      if(typeof value.options[i].optionvalue ==='object'){
                        for(var j in value.options[i].optionvalue){
                          if(value.options[i].optionvalue[j].language!==lang){
                            if(j==0){
                              value.options[i].optionvalue=value.options[i].optionvalue[j].option;
                            }
                          }
                      }
                     }
                    }
                }
            }
            if(typeof value==='object'){
               if(value.clientlabel!=""){
                for(var a in value.clientlabel){
                  if(value.clientlabel[a].language=='{{Lang::getLocale()}}'){
                     value.clientlabel=value.clientlabel[a].label;
                     break;
                  }
                }
              }
            }
            if(typeof value==='object'){
              if(value.agentlabel!=""){
                for(var a in value.clientlabel){
                  if(typeof value.clientlabel ==='object'){
                    if(value.clientlabel[a].language !=='{{Lang::getLocale()}}'){
                       if(a==0){
                        value.clientlabel=value.clientlabel[a].label;
                      }
                    }
                  }
                }
              }
            }
            if(typeof value==='object'){
                for(var a in value.errmsg){
                  if(value.errmsg[a].language=='{{Lang::getLocale()}}'){
                     value.errmsg=value.errmsg[a].label;
                     break;
                  }
                }
            }
            if(typeof value==='object'){
                for(var a in value.errmsg){
                  if(typeof value.errmsg ==='object'){
                    if(value.errmsg[a].language !=='{{Lang::getLocale()}}'){
                       if(a==0){
                        value.errmsg=value.clientlabel[a].label;
                      }
                    }
                  }
                }
            }
            if (value !== null && typeof value === "object") {
                    $.each(value, worker);
                }
            }
            $.each(data[g].nodes, worker);
      }
    }
            for(var i in $scope.tree3){
              if($scope.tree3[i].link&&$scope.linkLabel==$scope.tree3[i].api&&data.length>0){
                           $scope.tree3[i].options=data;
                           $scope.tree3[i].customerDisplay=false;
                        }
                        else if($scope.tree3[i].link&&$scope.linkLabel==$scope.tree3[i].api&&data.length==0){
                              $scope.tree3[i].customerDisplay=false;
                              $scope.tree3[i].clientlabel="";
                          }
                    }
            });
        }
     }
     $scope.nestedRadioRTL=function(){
          if('{{Lang::getLocale()}}'=='ar'){
                $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float','right');
                $(".rtlRadio").css({"margin-left":"0px","margin-right":"-70px"})
          }
     }
     
      $scope.updateSelection=function(position, value){
           if ($scope['checkBoxArray'+position].indexOf(value) == -1){
                    $scope['checkBoxArray'+position].push(value);
                    
                }
                else {
                    $scope['checkBoxArray'+position].splice($scope['checkBoxArray'+position].indexOf(value), 1);
                }
                if('{{Lang::getLocale()}}'=='ar'){
             $('.col-sm-1,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-sm-10,.col-sm-11,.col-sm-12').css('float','right');
             $(".rtlCheck").css({"margin-left":"0px","margin-right":"-54px"})
        }
      }
      
        
      $scope.getcusForm=function(e){
         console.log(e);
         $scope.reqoMail=e
         $scope.reqoMail.reqMail.$setValidity("server", true);
      }
      $scope.prevent=function($event){
             if ($event.stopPropagation) $event.stopPropagation();
        if ($event.preventDefault) $event.preventDefault();
        $event.cancelBubble = true;
        $event.returnValue = false;
        }
    
  $scope.getEditor=function(x,form){
      console.log(form);
    //console.log(vcRecaptchaService.getResponse());
          x.currentTarget.disabled=true;
          x.currentTarget.innerHTML="<i class='fa fa-circle-o-notch fa-spin fa-1x fa-fw'></i>Saving...";
          
               $scope.tree5=angular.copy($scope.tree3);
           
      
                     function walker(key, value) {
                 
                  delete this.placeholder;
                  delete this.clientlabel;
                  delete this.name;
                  delete this.agentRequiredFormSubmit;
                  delete this.agentDisplay;
                  delete this.customerDisplay;
                  delete this.customerRequiredFormSubmit;
                  delete this.api;
                  delete this.format;
                  delete this.agentlabel;
                  delete this.errmsg;
                  delete this.pattern;
                  delete this.linkHelpTopic;
                  delete this.link;
                  if(this.value!=undefined && this.type=='radio' && this.default=='no' && this.title!='Nested Radio'){
                        this[this.unique]=this.value;
                       delete this.unique; 
                       delete this.value;
                       delete this.type;
                       delete this.default;
                       delete this.title;
                       delete this.options;
                       
                  }
                  else if(this.value!=undefined && this.type=='radio' && this.default=='no' && this.title=='Nested Radio'){
                       // this[this.unique]=this.value;
                       
                       delete this.type;
                       delete this.default;
                       delete this.title;
                          for(var i in this.options){
                            if(this.value!=this.options[i].optionvalue){
                                  delete this.options[i]
                            }
                            else{ 
                                  //console.log(this.options[i].optionvalue);
                                  this[this.unique]=this.options[i].optionvalue;
                                  this.options[this.value]=this.options[i].nodes;
                                 
                                  if (typeof this.options[this.value] === "object"  && this.options[i].nodes !=0) {
                                       // Recurse into children
                                        $.each(this.options[this.value], walker);
                                  }
                                  
                                  delete this.options[i];
                            }
                          }
                       
                       delete this.value;
                        //delete this.options; 
                        delete this.unique;
                  }
                  else if(this.default=='no' && this.type=='select' &&  this.title=='Nested Select'){
                       //this[this.unique]=this.options;
                      //delete this.unique; 
                       delete this.type;
                       delete this.default;
                       delete this.title;
                       if(typeof this.value=="object"){
                         for(var i in this.options){
                            if(this.value.id!=this.options[i].id){
                                  delete this.options[i];
                            }
                            else{
                                //console.log(this.options[i].optionvalue);
                                this[this.unique]=this.options[i].optionvalue;
                                this.options[this.value.optionvalue]=this.options[i].nodes;
                                 
                                 if (typeof this.options[this.value.optionvalue] === "object" && this.options[i].nodes !=0) {
                                       // Recurse into children
                                        $.each(this.options[this.value.optionvalue], walker);
                                  }
                                  
                                  delete this.options[i];
                            }
                        }
                      }
                        else{
                            delete this.options;
                          }
                        
                      delete this.value;
                      // delete this.options;
                       delete this.unique;
                  }
                        else if(this.default=='yes' && this.type=='multiselect'){
                       //this[this.unique]=this.options;
                      //delete this.unique; 
                       delete this.type;
                       delete this.default;
                       delete this.title;
                    if(typeof this.value=="object"){
                       for(var i in this.options){
                            if(this.value.id!=this.options[i].id){
                                  delete this.options[i];
                            }
                            else{
                                //console.log(this.options[i].optionvalue);
                                this[this.unique]=this.options[i].id;
                                this.options[this.value.optionvalue]=this.options[i].nodes;
                                 
                                 if (typeof this.options[this.value.optionvalue] === "object" && this.options[i].nodes !=0) {
                                       // Recurse into children
                                        $.each(this.options[this.value.optionvalue], walker);
                                  }
                                  
                                  delete this.options[i];
                            }
                           }
                          }
                        else{
                            delete this.options;
                        }
                      delete this.value;
                      // delete this.options;
                       delete this.unique;
                  }
                  else if(this.default=='no' && this.type=='select' && this.title!='Nested Select'){
                       this[this.unique]=this.value.optionvalue;
                      delete this.unique; 
                       delete this.value;
                       delete this.type;
                       delete this.default;
                       delete this.title;
                       delete this.options;
                  }
                  else if(this.default=='no' && this.type=='checkbox' && this.title!='Nested Checkbox'){
                        var checkboxValue='';
                      for(var i in this.options){
                         if(this.options[i].checked==true){
                            checkboxValue+=this.options[i].optionvalue+',';
                         }
                      }
                       
                       this[this.unique]=checkboxValue;
                       delete this.unique; 
                       delete this.value;
                       delete this.type;
                       delete this.default;
                       delete this.title;
                       delete this.options;
                  }
                   else if(this.default=='no' && this.type=='checkbox' && this.title=='Nested Checkbox'){
                       //this[this.unique]=this.options;
                       var checkboxValue="";
                       delete this.type;
                       delete this.default;
                       delete this.title;
                       for(var i in this.options){
                          if(this.options[i].checked==false || this.options[i].checked==""){
                            delete this.options[i];
                         }
                         else{
                            
                            checkboxValue+=this.options[i].optionvalue+',';
                            this[this.unique]=checkboxValue;
                            this.options[this.options[i].optionvalue]=this.options[i].nodes;
                            
                            if (typeof this.options[this.options[i].optionvalue] === "object" && this.options[i].nodes.length !=0) {
                                       // Recurse into children
                                        $.each(this.options[this.options[i].optionvalue], walker);
                                  }
                              
                             delete this.options[i];
                         }
                       }
                       // delete this.options; 
                        delete this.unique;  
                  } 
                       else if(this.default=='yes' && this.type=="select"){
                        var val=$('#'+this.unique).val();
                        delete this.type;
                        if(typeof val!='object'&& this.value==""){
                           
                           this[this.unique]=val;
                          }
                        else{
                          this[this.unique]=this.value.id;
                        }
                        delete this.default;
                        delete this.title;
                        delete this.options;
                        delete this.unique; 
                        delete this.value;
                  }
                   else if(this.default=='yes' && this.title=="Subject"){
                        this[this.unique]=this.value;
                        delete this.unique; 
                        delete this.value;
                        delete this.type;
                        delete this.default;
                        delete this.title;
                        
                  }
                  else if(this.title=='Attachments'){
                       delete this.unique; 
                       delete this.value;
                       delete this.type;
                       delete this.default;
                       delete this.title;
                  }
                  else if(this.title=='Captcha'){
                       delete this.default;
                       delete this.title;
                       delete this.agentDisplay;
                       delete this.customerDisplay;
                  }
                  else{
                       this[this.unique]=this.value;
                       delete this.unique; 
                       delete this.value;
                       delete this.type;
                       delete this.default;
                       delete this.title;
                  }
                  
                  }
                  $.each($scope.tree5, walker);
                  console.log($scope.tree5);
                  $.each($scope.tree5, function(key,value){
                    if(this.options!=undefined){
                         for(var i in this.options){
                             $.each(this.options[i], function(key,value){
                                 if(typeof value=='object'){
                                     $.each(value, function(key,value){
                                            if(key==='options'){
                                               nest(value);
                                            }
                                            else{
                                               $scope.UserInfo[key]=value;
                                            }
                                     })
                                  }
                              })
                         }
                         delete this.options;
                      }
                  })
                  function nest(value){
                       for(var i in value){
                             $.each(value[i], function(key,value){
                                 if(typeof value=='object'){
                                     $.each(value, function(key,value){
                                            if(key==='options'){
                                               nest(value);
                                            }
                                            else{
                                               $scope.UserInfo[key]=value;
                                            }
                                     })
                                  }
                              })
                         }
                  }
                  $.each($scope.tree5, function(key,value){
                        if(typeof value=='object'){
                            $.each(value, function(key,value){
                                   $scope.UserInfo[key]=value; 
                                })
                        }
                  })
             console.log($scope.UserInfo);
         $scope.uploadArray.upload = Upload.upload({
                    url: "{!! url('ticket/'.$ticket->id.'/details') !!}",
                    data: $scope.UserInfo,
                }).success(function(data){
                  console.log(data);
            //x.currentTarget.innerHTML = 'Submit';
            $('.well').css('display','block');      
            $('.well').html(data.success);
            $('html, body').scrollTop(0);
            $('.well').css({'color':'white','background':'#5cb85c','border-color':'#4cae4c'});
            setTimeout(function(){
                 //location.reload();  
                 location.href= data.url
            },2000);
             //location.reload();
          })
          .error(function(data){
              if(data.error!=undefined){
                    $('.well').html(data.error);
              }
              else{
                  var res = "";
                $.each(data, function (idx, topic) {
                   res += "<li>" + topic + "</li>";
                });
                $('.well').html(res);
              }
                x.currentTarget.disabled=false;
                x.currentTarget.innerHTML = 'Submit';
                $('.well').css('display','block');      
                $('.well').css({'color':'white','background':'#dd4b39','border-color':'#d73925'});
                $('html, body').scrollTop(0);
            })
        }
    $scope.bou=0;
    $scope.kou=0;
        $scope.getFocus=function(x,z,a){
            if(x=='type'&&$scope.typelink!="1"){
            var y=z.currentTarget.id;
             $scope.bou++;
            var dependancy = x;
            if($scope.bou==1){
            $scope['loado'+a]=true;
            $http.get("{{url('ticket/form/dependancy?dependency=')}}"+dependancy).success(function (data) {
                 $('#'+y).attr('ng-focus',null).unbind('focus');
                 $('#'+y).attr('ng-click',null).unbind('click');
                 
                 function broker(key,value){
                    if(value!=null){
                     if(typeof value=="object" && value.type=="select"||value.type=="multiselect"){
                         if(value.unique==y){
                            this.options=data;
                            //console.log(value);
                         }
                         $.each(value,broker);
                     }
                   }
                 }
                 $.each($scope.tree3,broker);
                 $('#'+y).css('height', parseInt($('#'+y+' option').length) * 33);
                 //console.log($('#seletom'+y).css('height'));
                 $scope['loado'+a]=false;
                 $scope.bou=0;
            }).error(function(data){
                 $scope['loado'+a]=false;
                 $scope.bou=0;
                 alert("{!!Lang::get('lang.please_click_again')!!}");
            });
            }
          }
            if(x!='type'){
            var y=z.currentTarget.id;
             $scope.bou++;
            var dependancy = x;
            if($scope.bou==1){
            $scope['loado'+a]=true;
            $http.get("{{url('ticket/form/dependancy?dependency=')}}"+dependancy).success(function (data) {
                 $('#'+y).attr('ng-focus',null).unbind('focus');
                 $('#'+y).attr('ng-click',null).unbind('click');
                 
                 function broker(key,value){
                     if(value!=null){
                     if(typeof value=="object" && value.type=="select"||value.type=="multiselect"){
                         if(value.unique==y){
                            this.options=data;
                            //console.log(value);
                         }
                         $.each(value,broker);
                     }
                   }
                 }
                 $.each($scope.tree3,broker);
                 $('#'+y).css('height', parseInt($('#'+y+' option').length) * 33);
                 //console.log($('#seletom'+y).css('height'));
                 $scope['loado'+a]=false;
                 $scope.bou=0;
            }).error(function(data){
                 $scope['loado'+a]=false;
                 $scope.bou=0;
                 alert("{!!Lang::get('lang.please_click_again')!!}");
            });
            }
          }
          else{
            for(var u in $scope.tree3){
              if($scope.tree3[u].unique=='help_topic'){
              $http.get("{{ route('search.ticket-type.autofill') }}?ticket_create=1&helptopic="+$scope.tree3[u].value.id).success(function (datam) {
                                      for(var w in $scope.tree3){
                                        if($scope.tree3[w].api=='type'){
                                             $scope.tree3[w].options=datam; 
                                        }
                                      }
                                  })
            }
            }
          }
      
    }
     /*   $scope.getFocusApi=function(x,z,a){
            var y=z.currentTarget.id;
             $scope.kou++;
            var dependancy = x;
            if($scope.kou==1){
            $scope['loado'+a]=true;
            $http.get(dependancy,{ headers: { 'Access-Control-Allow-Origin': '*'}}).success(function (data) {
                 $('#'+y).attr('ng-focus',null).unbind('focus');
                 $('#'+y).attr('ng-click',null).unbind('click');
                 $.each($scope.tree3,boker);
                 function boker(key,value){
                     if(typeof value=="object" && value.title=="Api"){
                         if(value.unique==y){
                            this.options=data;
                            //console.log(value);
                         }
                         $.each(value,boker);
                     }
                 }
                 $('#'+y).css('height', parseInt($('#'+y+' option').length) * 33);
                 //console.log($('#seletom'+y).css('height'));
                 $scope['loado'+a]=false;
                 $scope.kou=0;
            })
            }
        }*/
});
</script>
@endpush

