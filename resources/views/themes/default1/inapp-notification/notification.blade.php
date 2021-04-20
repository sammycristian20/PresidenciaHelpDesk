<style type="text/css">
     @keyframes spinner {
        to {transform: rotate(360deg);}
    }
 
    .spinner:before {
        content: '';
        box-sizing: border-box;
        position: absolute;
        
        left: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border-radius: 50%;
        border: 2px solid #ccc;
        border-top-color: #333;
        animation: spinner .6s linear infinite;
    }
    .scrollable-ul { min-height:100px;max-height: 500px; overflow-y: auto;overflow-x: auto; }

    .notification-time { 
        font-size: 11px !important;
        font-weight: 400;
        margin: 3px;
    }
    .img-size-50{
        width: 50px;
        height: 50px;
        border: 3px solid #eee;
    }
</style>
<li class="nav-item dropdown"  ng-controller="MainCtrl" id="MainCtrl">
    <a href="#" class="nav-link" data-toggle="dropdown" ng-click="fbNotify()" title="In app Notifiaction">
        <i class="fas fa-bell in-app-icon"></i>
        <span class="badge badge-warning navbar-badge" ng-bind="notiNum" style="right: 1px !important;"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right scrollable-ul" when-scrolled="loadApi()">
        
        <span class="dropdown-header">Notifications</span>

        <!-- inner menu: contains the actual data -->
        
        <div id=seen@{{$index}}  ng-repeat="noti in notifications.data">

            <div class="dropdown-divider"></div>

            <a href="javascript:;" class="dropdown-item" ng-click="newTab(noti.url,noti.id)">
                
                <div class="media">
                    
                    <img ng-src="@{{noti.requester.profile_pic}}" onerror="this.src='{{assetLink('image','contacthead')}}'" 
                        alt="User Avatar" class="img-size-50 mr-3 img-circle">
                    
                    <div class="media-body">
                        
                        <h3 class="dropdown-item-title">
                            @{{noti.user}}
                        </h3>
                        
                        <p class="text-sm" ng-bind-html="$sce.trustAsHtml(noti.message)"></p>
                        
                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> @{{noti.created_at}}</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="spinner" ng-hide="showing" style="padding :35px;">
                 
        </div>
    </div>
</li>
@push('scripts')

<script>
    app.controller('MainCtrl', function($scope,$http, $sce,$window,$compile,$rootScope, $timeout){
        $scope.$sce=$sce;
        $scope.count=0;
        var count_api = "{!! url('notification/api/unseen/count') !!}/{{Auth::user()->id}}";
        
        $http.get(count_api).success(function(data){          
            if(data.count>9){
                    $scope.notiNum="9+";
            } else {
                $scope.notiNum=data.count;
            } 
        })
    
        $scope.fbNotify=function(){
            var notification_url = "{!! url('notification/api') !!}/{{Auth::user()->id}}";

            $http.get(notification_url).success(function(data){
                $scope.showing=true;
                $scope.notifications=data;

                for(var i in $scope.notifications.data){
                    if ($scope.notifications.data[i].requester == null) {
                        $scope.notifications.data[i]['user']= '{!! Lang::get("lang.system") !!}';
                        var $profile_pic = "{{assetLink('image','system')}}";
                        $scope.notifications.data[i]['requester']= {'profile_pic':$profile_pic};
                    } else if($scope.notifications.data[i].requester.changed_by_first_name==null&&$scope.notifications.data[i].requester.changed_by_last_name==null&&$scope.notifications.data[i].requester.changed_by_user_name==null){
                        $scope.notifications.data[i]['user']=$scope.notifications.data[i].by;
                    } else if($scope.notifications.data[i].requester.changed_by_first_name==""&&$scope.notifications.data[i].requester.changed_by_last_name==""&&$scope.notifications.data[i].requester.changed_by_user_name==""){
                        $scope.notifications.data[i]['user']=$scope.notifications.data[i].by;
                    } else if($scope.notifications.data[i].requester.changed_by_first_name==""&&$scope.notifications.data[i].requester.changed_by_last_name==""){
                       $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_user_name;
                    } else if($scope.notifications.data[i].requester.changed_by_first_name==null&&$scope.notifications.data[i].requester.changed_by_last_name==null){
                        $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_user_name;
                    } else if($scope.notifications.data[i].requester.changed_by_first_name==null){
                        $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_last_name;
                    } else if($scope.notifications.data[i].requester.changed_by_last_name==null){
                        $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_first_name;
                    } else{
                        $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_first_name+" "+$scope.notifications.data[i].requester.changed_by_last_name;
                    }
                }

                setTimeout(function(){ 
                    for(var i in $scope.notifications.data){
                        if($scope.notifications.data[i].seen=="0"){
                            var id='seen'+i;
                            var seenColor=document.getElementById(id);
                            seenColor.style.backgroundColor ="#edf2fa";
                        }
                    }
                }, 100);
            });
        };

        $scope.loadApi= function(){
            $scope.count++;
            $scope.showing=false; 
          
            if($scope.notifications.next_page_url==null){
                $scope.showing=true; 
            }
            
            if($scope.count==1){
                if($scope.notifications.next_page_url!=null){
            
                    $http.get($scope.notifications.next_page_url).success(function(data){
                        $scope.showing=true;
                        [].push.apply($scope.notifications.data, data.data);
                        $scope.notifications.next_page_url=data.next_page_url;
                
                        for(var i in $scope.notifications.data){
                  
                            if ($scope.notifications.data[i].requester == null) {
                                $scope.notifications.data[i]['user']= '{!! Lang::get("lang.system") !!}';
                                var $profile_pic = "{{assetLink('image','system')}}";
                                $scope.notifications.data[i]['requester']= {'profile_pic':$profile_pic};
                            } else if($scope.notifications.data[i].requester.changed_by_first_name==null&&$scope.notifications.data[i].requester.changed_by_last_name==null&&$scope.notifications.data[i].requester.changed_by_user_name==null){
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].by;
                            } else if($scope.notifications.data[i].requester.changed_by_first_name==""&&$scope.notifications.data[i].requester.changed_by_last_name==""&&$scope.notifications.data[i].requester.changed_by_user_name==""){
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].by;
                            } else if($scope.notifications.data[i].requester.changed_by_first_name==""&&$scope.notifications.data[i].requester.changed_by_last_name==""){
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_user_name;
                            } else if($scope.notifications.data[i].requester.changed_by_first_name==null&&$scope.notifications.data[i].requester.changed_by_last_name==null){
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_user_name;
                            } else if($scope.notifications.data[i].requester.changed_by_first_name==null){
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_last_name;
                            } else if($scope.notifications.data[i].requester.changed_by_last_name==null){
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_first_name;
                            } else{
                                $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_first_name+" "+$scope.notifications.data[i].requester.changed_by_last_name;
                            }
                        }

                        setTimeout(function(){ 
                            for(var i in $scope.notifications.data){
                            
                                if($scope.notifications.data[i].seen=="0"){
                                    document.getElementById('seen'+i).style.backgroundColor ="#edf2fa";
                                }
                            }
        
                        }, 100);
                    }).error(function (data, status, header, config) {
                        self.ResponseDetails = "Data: " + data;
                    });
                } else {
                    $scope.showing=true;
                }
            } else {
                setTimeout(function(){ 
                    if($scope.notifications.next_page_url!=null){
            
                        $http.get($scope.notifications.next_page_url).success(function(data){
                            $scope.showing=true;
                            [].push.apply($scope.notifications.data, data.data);
                            $scope.notifications.next_page_url=data.next_page_url;
              
                            for(var i in $scope.notifications.data){
                        
                                if ($scope.notifications.data[i].requester == null) {
                                    $scope.notifications.data[i]['user']= '{!! Lang::get("lang.system") !!}';
                                    var $profile_pic = "{{assetLink('image','system')}}";
                                    $scope.notifications.data[i]['requester']= {'profile_pic':$profile_pic};
                                } else if($scope.notifications.data[i].requester.changed_by_first_name==null&&$scope.notifications.data[i].requester.changed_by_last_name==null&&$scope.notifications.data[i].requester.changed_by_user_name==null){
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].by;
                                } else if($scope.notifications.data[i].requester.changed_by_first_name==""&&$scope.notifications.data[i].requester.changed_by_last_name==""&&$scope.notifications.data[i].requester.changed_by_user_name==""){
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].by;
                                } else if($scope.notifications.data[i].requester.changed_by_first_name==""&&$scope.notifications.data[i].requester.changed_by_last_name==""){
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_user_name;
                                } else if($scope.notifications.data[i].requester.changed_by_first_name==null&&$scope.notifications.data[i].requester.changed_by_last_name==null){
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_user_name;
                                } else if($scope.notifications.data[i].requester.changed_by_first_name==null){
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_last_name;
                                } else if($scope.notifications.data[i].requester.changed_by_last_name==null){
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_first_name;
                                } else{
                                    $scope.notifications.data[i]['user']=$scope.notifications.data[i].requester.changed_by_first_name+" "+$scope.notifications.data[i].requester.changed_by_last_name;
                                }
                            }    

                            setTimeout(function(){ 
                                for(var i in $scope.notifications.data){
                       
                                    if($scope.notifications.data[i].seen=="0"){
                                        document.getElementById('seen'+i).style.backgroundColor ="#edf2fa";
                                    }
                                }
                            }, 100);
                        }).error(function (data, status, header, config) {
                            self.ResponseDetails = "Data: " + data;
                        });
                    }
                }, 5000);
            }
        };
  
        $scope.newTab=function(x,y){
            var url=x;
            var config={
                params:{
                    notification_id:y
                }
            }
            var api = "{!! url('notification/api/seen') !!}/{{Auth::user()->id}}";
            
            $http.get(api,config).success(function(data){
                //alert("success");  
            }).error(function(data){
              //alert("failed");
            });
             
            if(url==""||url==null){
               //alert("sorry");
            } else {
                $window.open(x, '_blank');
            }

            var count_api = "{!! url('notification/api/unseen/count') !!}/{{Auth::user()->id}}";
        
            $http.get(count_api).success(function(data){          
                if(data.count>9){
                        $scope.notiNum="9+";
                } else {
                    $scope.notiNum=data.count;
                } 
            })
        }
          
          
        /**
         * Wyswyg editor
         */ 
        $rootScope.disable=true;
        $rootScope.inlineImage=true;
        $rootScope.arrayImage=[];
        $rootScope.attachmentImage=[];
        $rootScope.inlinImage=[];
        $rootScope.storageDirectoryObj = [];

        $scope.getImageApi=function(){
            localStorage.setItem('mediaURL', "{{url('media/files')}}");

            $http.get("{{url('media/files')}}").success(function(data){
                $rootScope.arrayImage=data;
                $scope.apiCalled=true;
            }).error(function(data){
               $scope.apiCalled=true;
            });
            getStorageDirectories();
        };

        function getStorageDirectories() {
            $http.get("{{url('api/get-storage-directories')}}").success(function(data){
                if(data.data) {
                $rootScope.storageDirectoryObj = data.data;
                } else {
                    $rootScope.storageDirectoryObj = [];
                    }
                $scope.apiCalled = true;
                }).error(function(err){
                $scope.apiCalled = true;
                console.error('error in getImageApi function ', err); 
                })
            };

        function bytesToSize(bytes) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            
            if (bytes == 0) return '';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        };
                        
        function gcd (a, b) {
            return (b == 0) ? a : gcd (b, a%b);
        };
                        
        $scope.insert = function(x, i, pathname, name, z,j,h,w,date,extension) {
            if (z != 0) {
                $('label[for="happy0"]>img').css({'border': 'none', 'box-shadow': 'none'});
            } else {
                $('label[for="happy0"]>img').css({'border': '1px solid #fff', 'box-shadow': '0 0 0 4px #0073aa'});
            }
            
            if (z == 1) {
                $('label[for="happy1"]>img').css({'border': '1px solid #fff', 'box-shadow': '0 0 0 4px #0073aa'});
            } else {
                $('label[for="happy1"]>img').css({'border': 'none', 'box-shadow': 'none'});
            }
                            
            $rootScope.disable = false;
            $rootScope.preview = true;
            $rootScope.ext = x;
            $rootScope.imgName = extension;
            $rootScope.viewImage = $rootScope.arrayImage[i]
            if (x == "image") {
                var r = gcd (w, h);
                $rootScope.widthRatio=w/r;
                $rootScope.heightRatio=h/r;
                $rootScope.mediaWidth=w;
                $rootScope.mediaHeight=h;
                $rootScope.mediaImage={'width':w,'height':h};
                $rootScope.inlineImage = false;
                $rootScope.viewImage = i;
                $rootScope.pathName = pathname;
                $rootScope.fileName = name;
                $rootScope.fileSize=bytesToSize(j);
                $rootScope.privewSize=true;
                $rootScope.modifiedMedia=date;
            } else if (x == "text") {
                $rootScope.inlineImage = true;
                $rootScope.viewImage = "{{assetLink('image','text')}}";
                $rootScope.pathName = pathname;
                $rootScope.fileName = name;
                $rootScope.fileSize=bytesToSize(j);
                $rootScope.privewSize=false;
                $rootScope.modifiedMedia=date;
            } else {
                $rootScope.inlineImage = true;
                $rootScope.viewImage = "{{assetLink('image','common')}}";
                $rootScope.pathName = pathname;
                $rootScope.fileName = name;
                $rootScope.fileSize=bytesToSize(j);
                $rootScope.privewSize=false;
                $rootScope.modifiedMedia=date;
            }
        }
                        
        $scope.widthChange=function(x){
            var v=(x/$rootScope.widthRatio)*$rootScope.heightRatio;
            $rootScope.mediaImage.height=Math.round(v);
        };

        $scope.heightChange=function(x){
            var v=(x/$rootScope.heightRatio)*$rootScope.widthRatio;
            $rootScope.mediaImage.width=Math.round(v);
        };

        $scope.noInsert=function(){
            $rootScope.disable=true;
            $rootScope.inlineImage=true;
            $rootScope.preview=false;
            $rootScope.privewSize=false;
            $('input[type="radio"]:checked + label>img').css({'border': 'none','box-shadow': 'none'});
        }
      
        $scope.pushToEditor=function(){
            var radios = document.getElementsByName('selection');
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[i].checked) {
                    var attaremove=$rootScope.arrayImage.data[i].filename;
                    var index = $scope.fileAlreadyExists($rootScope.arrayImage.data[i]);
                    if(index < 0) {
                        $scope.updateInlineAttachment($rootScope.arrayImage.data[i], 'attachment');
                    } else {
                        delete $rootScope.attachmentImage[index].category;
                    }
                }
            }
        }

        $scope.pushCannedToEditor=function(data) {
            $.grep($rootScope.attachmentImage, function(e){
                if (e.hasOwnProperty('category')) {
                    $timeout(function() {
                        var el = document.getElementById(e.category+e.filename);
                        angular.element(el).triggerHandler('click');
                    },0);
                }
            });
            $.each(data, function($key,$item) {
                if($scope.fileAlreadyExists($item) >= 0) {
                    //continure without entering the item
                    return true;
                }
                $scope.updateInlineAttachment($item, 'canned');
            });
        }

        $scope.updateInlineAttachment = function($item, $Attachmentype){
            $rootScope.attachmentImage.push($item);
            $compile($("#file_details").append("<div type='hidden' id='hidden' style='background-color: #f5f5f5;border: 1px solid #dcdcdc;font-weight: bold;margin-top:9px;overflow-y: hidden;padding: 4px 4px 4px 8px;max-width: 448px;' contenteditable='false'>"+$item.filename+"("+$item.size+"bytes)<i class='fas fa-times' id='"+$Attachmentype+$item.filename+"' aria-hidden='true' style='float:right;cursor: pointer;' ng-click='remove($event)'></i></div>"))($scope);
        };

        $scope.fileAlreadyExists = function(fileObj)
        {
            result = -1;
            $.each($rootScope.attachmentImage, function($key, $item){
                if($item.pathname == fileObj.pathname && $item.size == fileObj.size) {
                    result = $key;
                    return false;
                }
            });
            return result;
        }

        $scope.deleteToLibrary=function(){
            var radios = document.getElementsByName('selection');
            $scope.deleteFile={};
            
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[i].checked) {
                    $scope.deleteFile['file']=$rootScope.arrayImage.data[i].pathname;
                        
                    $http.post('{{url("media/files/delete")}}',$scope.deleteFile).success(function(data){
                        alert(data[0]);
                        $rootScope.preview=false;
                        $rootScope.privewSize=false;

                        $http.get("{{url('media/files')}}").success(function(data){
                            $rootScope.arrayImage=data;
                        });
                    });
                }
            }
        }

        $scope.pushImage = function(x,y) {
            var radios = document.getElementsByName('selection');
            
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[i].checked) {
                    var path=$rootScope.arrayImage.data[i].base_64.split(' ');
                    var joinPath=path.join("%20");
                    CKEDITOR.instances['reply_content'].insertHtml("<img  src=" + joinPath + " alt='" + $rootScope.arrayImage.data[i].filename + "' width='"+x+"' height='"+y+"' />");
                }
            }
        }

        $scope.remove=function(x) {
            var id=x.currentTarget.parentNode;
            id.remove();
            var value=x.currentTarget.parentNode.innerHTML;
            var b=value.split('(');
            $rootScope.attachmentImage=$.grep($rootScope.attachmentImage, function(e){
                return e.filename != b[0];
            });
        }

        $scope.getEditor=function(){
            $("#t1").hide();
            $("#show3").show();
            $scope.editor=CKEDITOR.instances.reply_content.getData();
            $scope.imagesAlt=[];     
            $("<div>" + $scope.editor + "</div>").find('img').each(function(i) {
                $scope.imagesAlt.push(this.alt);
            });
            
            for(var i in $scope.imagesAlt){
                if ($rootScope.arrayImage=="") continue; //escape execution as data is not valid
             
                var x=$.grep($rootScope.arrayImage.data, function(e){
                    return e.filename == $scope.imagesAlt[i];
                });
                if(typeof x[0] != "undefined") {
                    $rootScope.inlinImage.push(x[0]);
                }
            }
            $("<div>" + $scope.editor + "</div>").find('img').each(function(i) {
                var old=this.src;
                $scope.editor1=$scope.editor.replace(old, $scope.imagesAlt[i]);
                $scope.editor=$scope.editor1;
            });
             
            if($("<div>" + $scope.editor + "</div>").find('img').length==0){
                $rootScope.inlinImage = [];
                if($scope.editor=='<p><br></p>'){
                    $scope.editor1="";
                } else{
                    $scope.editor1=$scope.editor;
                }
            } else if($("<div>" + $scope.editor + "</div>").find('img').length != $rootScope.inlinImage){
                $rootScope.inlinImage = $.grep($rootScope.inlinImage, function($item){
                    return ($scope.imagesAlt.indexOf($item.filename) >= 0) ? true : false;
                });
            }

            $rootScope.inlinImage.forEach(function(v){ delete v.base_64 });
            $rootScope.attachmentImage.forEach(function(v){ delete v.base_64 });
            var serialize=$("#form3").serialize();
            $scope.editorValues={};
            $scope.editorValues['content']=$scope.editor1;
            $scope.editorValues['inline']=$rootScope.inlinImage;
            $scope.editorValues['attachment']=$rootScope.attachmentImage;
            var config={
                headers : {
                    'Content-Type' : 'application/json'
                }
            }
            var url = "{{url('/thread/reply')}}?"+serialize;
            $http.post(url,$scope.editorValues,config).success(function(data){
                if(data.result.success!=null){
                    document.getElementById('reply_content').value = '';
                    location.reload();
                }
            }).error(function(data){
                $("#show3").hide();
                $("#t1").show();
                var res = "";
                $.each(data, function (idx, topic) {
                    res += "<li>" + topic + "</li>";
                });
                $('html, body').animate({scrollTop: $('#aa').offset().top}, 500);
                $("#reply-response").html("<div class='alert alert-danger'><strong>Whoops!</strong> There were some problems with your input.<br><br><ul>" +res+ "</ul></div>");
            });
        
        }

        $scope.callApi=function(){         
            $scope.api2Called=true;
            
            if($rootScope.arrayImage.next_page_url==null){
                $scope.api2Called=false;   
            } else{
                $http.get($rootScope.arrayImage.next_page_url).success(function(data){
                    $scope.api2Called=false;
                    [].push.apply($rootScope.arrayImage.data, data.data);
                    $rootScope.arrayImage.next_page_url=data.next_page_url;
                });
            }
        }

        $scope.filterApi=function(x){
            var filter={};
            
            if(x.year==undefined || x.year==""){
                filter['year']="";
            } else {
                filter['year']=x.year;
            }

            if(x.month==undefined || x.month==""){
                filter['month']="";
            } else {
                filter['month']=x.month;
            }
     
            if(x.day==undefined || x.day==""){
                filter['day']="";
            } else {
                filter['day']=x.day;
            }

            if(x.type==undefined || x.type==""){
                filter['type']="";
            } else{
                filter['type']=x.type;
            }
        
            if(filter.type==""&&filter.year==""&&filter.month==""&&filter.day!=""){
                alert('Please Select a Particular Month and Year')
            } else if(filter.type==""&&filter.year==""&&filter.month!=""&&filter.day!=""){
                alert('Please Select a Particular Year')
            } else if(filter.type==""&&filter.year==""&&filter.month!=""&&filter.day==""){
                alert('Please Select a Particular Year')
            } else {
                var config={
                    params:filter
                }

                $http.get("{{url('media/files')}}",config).success(function(data){
                    $rootScope.arrayImage=data;
                    $rootScope.preview=false;
                    $rootScope.disable=true;
                    $rootScope.inlineImage=true;
                    $rootScope.privewSize=false;
                    $('input[type="radio"]:checked + label>img').css({'border': 'none','box-shadow': 'none'});
                }).error(function(data){
                    $rootScope.arrayImage['data']=[];
                    $rootScope.preview=false;
                    $rootScope.disable=true;
                    $rootScope.inlineImage=true;
                    $rootScope.privewSize=false;
                    $('input[type="radio"]:checked + label>img').css({'border': 'none','box-shadow': 'none'});
                });
            }   
        }

        $scope.pushInlineAttachments=function(data){
            $rootScope.inlinImage =[];
            $.each(data, function($key, $item){
                $rootScope.inlinImage.push($item);
            });
        }
    });
</script>
@endpush