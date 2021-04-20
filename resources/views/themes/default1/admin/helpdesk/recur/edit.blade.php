<?php
    $relativeUrl = str_replace(\URL::to('/'), '', \Request::url());
    $layout = (strpos($relativeUrl, 'agent') !== false) ? 'themes.default1.agent.layout.agent' : 'themes.default1.admin.layout.admin';
?>
@extends($layout)
@section('PageHeader')
<h1>{{Lang::get('lang.ticket_recurring')}}</h1>
@stop
@section('custom-css')
<link href="{{assetLink('css','bootstrap-datetimepicker4')}}" rel="stylesheet" media="none" onload="this.media='all';" />
<link href="{{assetLink('css','select2')}}" rel="stylesheet" media="none" onload="this.media='all';"/>
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
.select2-container--default .select2-selection--multiple{
  height: 34px;
    border-radius: 0px !important;
    border: 1px solid #d2d6de !important;
    overflow: auto;
}
</style>
@stop

@section('content')
@include('themes.default1.common.recaptcha')

    <div id="app-admin">
    @php
        $relativeUrl = str_replace(\URL::to('/'), '', \Request::url());
        $panel = (strpos($relativeUrl, 'agent') !== false) ? 'agent' : 'admin';
    @endphp
  <faveo-form category="ticket" panel="{!! $panel !!}" scenario="recur"></faveo-form>
</div>
{{-- REASON: it is getting used in agent panel too but this bundle is admin.js. So in agent layout, this bundle wouldn't exist --}}
<script src="{{bundleLink('js/admin.js')}}" type="text/javascript"></script>
@stop
@push('scripts')
<script src="{{assetLink('js','moment')}}"></script>
<!--date time picker-->
<script src="{{assetLink('js','select2')}}"></script>
@endpush
