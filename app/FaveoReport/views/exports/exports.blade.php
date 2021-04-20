@extends('themes.default1.agent.layout.agent')
@section('Staffs')
active
@endsection

@section('staffs-bar')
active
@endsection

@section('Report')
class="active"
@endsection

@section('HeadInclude')
<style type="text/css">
</style>
@endsection

@section('PageHeader')
<h1>{!! Lang::get('report::lang.activity-download') !!}</h1>
@endsection

@section('content')
<div id="app-agent">
    <report-exports></report-exports>
</div>
<script src="{{bundleLink('js/agent.js')}}" type="text/javascript"></script>

@endsection

@section('FooterInclude')
<script type="text/javascript">
</script>
@endsection
