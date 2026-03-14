@extends('template::layouts.app')

@section('title', __('errors.500_title'))

@section('content')
@include('template::errors.500')
@endsection
