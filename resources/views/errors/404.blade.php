@extends('template::layouts.app')

@section('title', __('errors.404_title'))

@section('content')
@include('template::errors.404')
@endsection
