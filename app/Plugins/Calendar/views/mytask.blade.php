@extends('themes.default1.agent.layout.agent')
<style>
  .popover-title {
    font-weight: 700;
    font-size: 12px;
    padding: 8px 24px 8px 14px;
    position: relative;
}
.deadline-desc .tm {
    color: #666;
    font-weight: 700;
}
.add-a-task {
    position: relative;
}
form.add-task {
    margin: 0;
    position: relative;
}
form.add-task #add-task-input {
    margin: 0;
    -webkit-border-radius: 0;
    -moz-border-radius: 0;
    border-radius: 0;
    border-left: 0;
    border-right: 0;
    border-color: #bbb;
    padding: 8px 20px 8px 25px;
    font-weight: 500;
    width: 100%;
}
input[type=text]{
    background-color: #fff;
    border: 1px solid #ccc;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -moz-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border linear .2s,box-shadow linear .2s;
    -moz-transition: border linear .2s,box-shadow linear .2s;
    -o-transition: border linear .2s,box-shadow linear .2s;
    transition: border linear .2s,box-shadow linear .2s;
}
input[type=text]{

    display: inline-block;
    height: 20px;
    padding: 4px 6px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 20px;
    color: #555;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    vertical-align: middle;
}
input[type=text]:focus { 
     background-color: aliceblue;
     outline: none;
}
.fa-plus{
    position: absolute;
    top: 14px;
    left: 10px;
    color: #5ca77c;
    font-size: 12px;
    cursor: pointer;
}

.fa-times{
    position: absolute;
    top: 8px;
    right: 10px;
    font-size: 18px;
    color: #ccc;
    cursor: pointer;
}
.fc-day-grid-event{
    border-color: inherit !important;
    background-color: inherit !important;
}
.fc-content {
    background-color: #eaf9e2 !important;
}
.fc-content {
    background: rgba(0,0,0,.04);
    border: 1px solid rgba(0,0,0,.08) !important;
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
    -moz-box-shadow: 0 1px 1px rgba(0,0,0,.04);
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px;
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
}
.fc-event .fc-event-inner {
    width: auto;
    height: auto;
}
.fc-event-inner {
    width: 100%;
    height: 100%;
    overflow: hidden;
}
.fc-avatars img {
    margin: -2px 0 0 -2px;
    border: 2px solid rgba(0,0,0,.15);
    width: 18px;
    height: 18px;
    cursor: pointer;
    display: inline-block;
    background: #fff;
    margin: 2px 0;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    border-radius: 100%;
    float: left;
}
 .fc-title {
    line-height: 2;
    color: #333;
    font-size: 13px;
    font-weight: 600;
}
.task-details {
    height: 100%;
    background: #fff;
}
.task-details-header {
    position: relative;
}
.tasks-list-header, .task-details-header {
    background: #fcfcfc;
    padding: 6px 10px;
    height: 50px;
    border-bottom: 1px solid #e8e8e6;
}
.pull-right {
    float: right;
}
.task-details-header .btn {
    margin-right: 25px!important;
}
.tasks-list-header .btn, .task-details-header .btn {
    margin-top: 2px;
    font-weight: 700;
}
.cl{
    font-size: 22px;
    color: #ccc;
    cursor: pointer;
    margin: 4px 0 0 4px;
    display: block;
}
.td-content .visibility {
    background: #f8f8f8;
    border-bottom: 1px solid #efefef;
    font-size: 11px;
    line-height: 16px;
    color: #999;
    padding: 4px 15px;
}
.td-content .title-header {
    font-weight: 700;
    font-size: 18px;
    line-height: 140%;
    position: relative;
    padding: 10px 15px 10px 73px;
}
.td-content .title-header .checkmark {
    position: absolute;
    cursor: pointer;
    top: 11px;
    left: 14px;
    z-index: 2;
}
.td-content .title-header #editTaskTitle {
    border: 0;
    padding: 0;
    width: 100%;
    font-weight: 700;
    font-size: 18px;
    line-height: 140%;
    color: #333;
    font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
    resize: none;
    background: #eee;
    -webkit-border-radius: 0;
    -moz-border-radius: 0;
    border-radius: 0;
    margin: 0;
}
#task-details-container .td-content .td-attributes {
    background-color: #fff;
}
.td-content .pane-attribute-section {
    cursor: pointer;
    padding: 8px 15px 8px 120px;
    position: relative;
}
.pane-attribute-section .pane-label {
    color: #666;
    cursor: pointer;
    position: absolute;
    font-size: 12px;
    left: 15px;
    height: 24px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 700;
    line-height: 24px;
    vertical-align: middle;
    width: 100px;
    margin: 0;
}
.pane-attribute-section .attribute-value {
    display: block;
    min-height: 24px;
}
.pane-attribute-section:hover {
    background: #f8f8f8;
    -webkit-box-shadow: 0 0 3px rgba(0,0,0,.2);
    -moz-box-shadow: 0 0 3px rgba(0,0,0,.2);
    box-shadow: 0 0 3px rgba(0,0,0,.2);
}
.td-content .pane-attribute-section {
    cursor: pointer;
    padding: 8px 15px 8px 120px;
    position: relative;
}
.filled-label-user {
    float: left;
    position: relative;
    display: block;
    font-weight: 500;
    background: #fff;
    padding: 1px 7px 1px 21px;
    border: 1px solid #e5e5e5;
    vertical-align: baseline;
    margin: 2px 6px 3px 0;
    white-space: nowrap;
    line-height: 15px;
    -webkit-border-radius: 24px 5px 5px 24px;
    -moz-border-radius: 24px 5px 5px 24px;
    border-radius: 24px 5px 5px 24px;
}
.filled-label-user .avatar {
    width: 16px;
    height: 16px;
    margin-left: -20px;
    display: block;
    float: left;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    border-radius: 100%;
}
.filled-label-user .name {
    font-size: 11px;
    color: #454545;
    max-width: 200px;
    display: block;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.attribute-value .deadline-value {
    color: #666;
    font-size: 15px;
    line-height: 24px;
    font-weight: 600;
}
.pane-attribute-section .pane-label i {
    margin: 0 5px 0 0;
}
.td-content .td-notes {
    position: relative;
    padding: 0;
    background: #fff;
}
.td-content .td-notes .add-note {
    background: #fff;
    margin: 0;
    padding-bottom: 8px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    position: relative;
    -webkit-box-shadow: 0 0 2px rgba(0,0,0,.1);
    -moz-box-shadow: 0 0 2px rgba(0,0,0,.1);
    box-shadow: 0 0 2px rgba(0,0,0,.1);
}
.td-content .td-notes .add-note .top {
    position: relative;
}
.td-content .td-notes .add-note .top a.toggle-notes-layout {
    position: absolute;
    width: 16px;
    height: 16px;
    padding-left: 10px;
    top: 16px;
    right: 15px;
}
.td-content .td-notes .add-note .top a.toggle-notes-layout .icon-fullscreen, .td-content .td-notes .add-note .top a.toggle-notes-layout .icon-resize-small {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
    float: left;
}
.td-content .td-notes textarea {
    resize: none;
    padding-right: 30px;
    margin: 8px 8px 0 47px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
}
.td-content .td-notes .add-note img.my-avatar, .td-content .td-notes .add-note b.inset-avatar {
    position: absolute;
    top: 8px;
    left: 8px;
    display: block;
    width: 30px;
    height: 30px;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    border-radius: 100%;
}
.td-content .td-notes .add-note b.inset-avatar {
    position: absolute;
    top: 8px;
    left: 8px;
    display: block;
    width: 30px;
    height: 30px;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    border-radius: 100%;
}
.td-content .td-notes .add-comment-options .btn-post {
    margin-right: 8px;
}
.fa-sort-desc{
    position: absolute;
    right: 8px;
    top: 6px;
}
.typeahead-frame {
    display: inline-block;
    position: relative;
}
.typeahead {
    z-index: 1051;
    margin-top: 2px;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
 .popover-content h5, .popover .labels .popover-content h5, .popover .follower .popover-content h5 {
    margin: 0 0 5px;
    color: #666;
    font-size: 13px;
    border-bottom: 1px solid #eee;
}
.popover ul {
    margin: 0;
    padding: 0;
    clear: both;
}
.popover-content #deadline-form {
    padding: 0;
    margin: 0;
}
.popover .popover-content #deadline-form .row-deadline {
    height: 45px;
    position: relative;
}
.popover  .popover-content #deadline-form .row-popover-deadline {
    border-bottom: 1px solid #d8d8d8;
}
.popover  .popover-content #deadline-form .row-deadline .allday {
    width: 30%;
    border-right: 1px solid #d8d8d8;
}
.popover  .popover-content #deadline-form .row-deadline>div {
    float: left;
    padding: 5px 10px;
    height: 45px;
}
.popover  .popover-content #deadline-form .row-deadline .allday label {
    margin-top: 9px;
    font-size: 12px;
    line-height: 2;
}
label {
    display: block;
    margin-bottom: 5px;
}

.popover  .popover-content #deadline-form .row-deadline .allday label input {
    margin-right: 3px;
}
.popover  .popover-content #deadline-form input, .popover .deadline .popover-content #deadline-form select {
    margin: 0;
    padding: 0!important;
    border: 0;
    background: 0 0;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.popover .popover-content #deadline-form .row-deadline .date-box {
    width: 40%;
    position: relative;
    padding-left: 20px;
}
.popover .popover-content #deadline-form span.section-title {
    color: #a1a1a1;
    font-size: 11px;
    display: block;
}
.popover  .popover-content #deadline-form .row-deadline .date-box input.deadline {
    cursor: pointer;
    font-weight: 500;
    font-size: 11px;
    width: 100%;
    box-shadow: none;
}
.popover .popover-content #deadline-form input, .popover .popover-content #deadline-form select {
    margin: 0;
    padding: 0!important;
    border: 0;
    background: 0 0;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.popover  .popover-content #deadline-form .row-deadline .time-box {
    position: relative;
    width: 30%;
    border-left: 1px solid #d8d8d8;
}
.popover .popover-content #deadline-form .row-deadline .time-box input.deadline {
    cursor: pointer;
    font-size: 11px;
    font-weight: 500;
    box-shadow: none;
}
.popover .popover-content #deadline-form .row-deadline .time-box input {
    width: 100%;
}
.popover .popover-content #deadline-form .row-alert {
    height: 42px;
}
.popover .popover-content #deadline-form .row-popover-deadline {
    border-bottom: 1px solid #d8d8d8;
}
.popover .popover-content #deadline-form .row-alert .alert-box {
    width: 60%;
}
.popover .popover-content #deadline-form .row-alert>div {
    padding: 5px 10px;
    height: 40px;
    float: left;
}
.popover .popover-content #deadline-form span.section-title {
    color: #a1a1a1;
    font-size: 11px;
    display: block;
}
.popover .popover-content #deadline-form .row-alert>div select {
    height: auto;
    margin-left: -8px;
    width: 100%;
    font-weight: 500;
}
.popover .popover-content #deadline-form input, .popover .deadline .popover-content #deadline-form select {
    margin: 0;
    padding: 0!important;
    border: 0;
    background: 0 0;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.popover .popover-content .muted {
    font-weight: 400!important;
    color: #aaa;
}
.popover .popover-content #deadline-form .row-alert .timezone-box {
    width: 40%;
    border-left: 1px solid #d8d8d8;
}
.popover .popover-content #deadline-form .row-alert>div {
    padding: 5px 10px;
    height: 40px;
    float: left;
}
.popover .popover-content #deadline-form .row-alert .timezone-box select {
    
    font-weight: 500;
    font-size: 11px;
}
.popover .popover-content #deadline-form .row-alert>div select {
    height: auto;
    margin-left: -8px;
    width: 100%;
    font-weight: 500;
}
.popover .popover-content #deadline-form input, .popover .deadline .popover-content #deadline-form select {
    margin: 0;
    padding: 0!important;
    border: 0;
    background: 0 0;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.popover .popover-content .muted {
    font-weight: 400!important;
    color: #aaa;
}
.popover .popover-content #deadline-form .row-recurring {
    height: 45px;
}
.popover .popover-content #deadline-form .row-popover-deadline {
    border-bottom: 1px solid #d8d8d8;
}
.popover .popover-content #deadline-form .row-recurring>div {
    padding: 5px 10px;
    height: 40px;
    float: left;
}
.popover .popover-content #deadline-form .form-actions {
    margin: 0;
    padding-top:5px;
}
.form-actions {
    margin-top: 20px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border-top: 1px solid #e5e5e5;
    height: 40px;
}
.popover .popover-content #deadline-form .form-actions .deadline-remove {
    color: #e44545;
    font-size: 12px;
    display: none;
}
.popover .popover-content #deadline-form .form-actions button {
    margin-left: 5px;
}
.bottom .popover-content{
    padding: 0px !important;
}
.fc-time{
    color: black;
}
</style>
@section('content')
<div class="box box-primary">
    <div class="box-header">
        
        <h3 class="box-title" >My Task</h3>
        <div class="pull-right">
           <button class="btn btn-default"><a href="javascript:void(0)"><i class="fa fa-calendar" aria-hidden="true"></i> Calender</a></button>
        </div>
        
            <div class="add-a-task" style="margin-top: 30px">
                <form class="form-inline add-task" onsubmit="return false;">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      <input type="text" id="add-task-input" class="input-large" placeholder="Type here to create a task..." onkeypress="projectTitle(event)">
                </form>
            </div>
        
    </div>
    <!-- ticket details Table -->
    <div class="box-body">
        

    </div>
</div>
@stop
