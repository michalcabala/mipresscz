@extends('template::layouts.app')

@section('title', ($entry->meta_title ?? null) ?: config('app.name', 'miPress'))
@section('description', $entry->meta_description ?? __('Moderní CMS postavené na Laravelu 12, Filamentu 5 a Tailwind CSS. Strukturovaný obsah, blokový editor a vícejazyčnost.'))

@section('content')
@if(!empty($entry->content))
    {!! mason(content: $entry->content, bricks: $bricks ?? [])->toHtml() !!}
@endif
@endsection
