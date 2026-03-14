@extends('template::layouts.app')

@section('title', __('errors.503_title'))

@section('content')
@include('template::errors.503')
@endsection
