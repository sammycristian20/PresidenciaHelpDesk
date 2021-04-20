@extends('themes.default1.admin.layout.admin')

@section('Manage')
active
@endsection

@section('manage-bar')
active
@stop

@section('approval-workflow')
class="active"
@endsection

@section('HeadInclude')
<style>
    .currency-select .currency-select__flag-currency-code .currency-select__currency-code{
             font-size: 14px !important;
        }
    .bootstrap-select>.dropdown-toggle{
             background-color: #FFF;
             border-color: #A8A8A8;
        }
    .bootstrap-select.btn-group .dropdown-toggle .caret{
             margin-top: 0px;
        }
</style>
@endsection
<!-- header -->
@section('PageHeader')
<h1>
    {!! trans('lang.edit-approval-workflow') !!}
</h1>
@endsection
<!-- breadcrumbs -->
@section('breadcrumbs')
<ol class="breadcrumb">
</ol>
@endsection
<!-- /breadcrumbs -->
@section('content')

<div id="app-admin">
  <approval-workflow></approval-workflow>
</div>
@endsection

@section('FooterInclude')
<script type="text/javascript">
</script>

@endsection
