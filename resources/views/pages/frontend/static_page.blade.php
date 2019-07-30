@extends('layouts/frontend/static_page')

@section('css')

@endsection

@section('content')
<div class="container">
    <div class="title">
        <h1>{{ parse_json_multilang($static_page->title) }}</h1>
		<div class="hover-line"></div>
    </div>
    <div class="content margin-top-10">
       {!! parse_json_multilang($static_page->content) !!}
    </div>
</div>
@endsection