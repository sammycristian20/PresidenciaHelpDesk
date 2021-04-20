@extends('themes.default1.agent.layout.agent')
@section('content')
<div class="hfeed site" id="page">
    <article class="hentry error404 text-center">
        <h1 class="error-title">
            {!! Lang::get('lang.invalid_url') !!}
        </h1>
        <h2 class="entry-title text-muted">
            {!! Lang::get('lang.invalid_url_msg') !!}
        </h2>
    </article>
</div>
@stop
