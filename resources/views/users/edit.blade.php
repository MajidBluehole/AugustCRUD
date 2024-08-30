@extends('layouts.app')

@section('content')
    @include('users.form', ['user' => $user, 'countries' => $countries, 'states' => $states, 'cities' => $cities])
@endsection
