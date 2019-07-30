@extends('layouts/frontend/index')

@section('content')
<div class="row">
    <div class="col-xs-12">
      <div class="page-content container">
            <div class="title">
               <h1>FAQ</h1>
            </div>
          <div class="content margin-top-10">
                 This section is occupied by FAQ!
          </div>
      </div>
  </div>
</div>
@endsection

@section('js')
  <script>
    var data = {
      loadUrl: "{{ route('faq.load') }}",
    };
  </script>
@endsection