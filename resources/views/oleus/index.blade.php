@extends('html')

@section('body')
    @include('oleus.base.offcavas')
    <div class="uk-container">
        @include('oleus.base.navbar')
        @yield('page')
    </div>
@endsection