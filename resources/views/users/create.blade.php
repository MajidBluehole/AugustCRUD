@extends('layouts.app')

@section('content')
    @include('users.form', ['countries' => $countries, 'states' => [], 'cities' => []])
@endsection
