@extends('template::layouts.app')

@section('title', '500 – Chyba serveru')

@section('content')
@include('template::errors.500')
@endsection
