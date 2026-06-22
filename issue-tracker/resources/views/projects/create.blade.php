@extends('layouts.app')
@section('title', 'New project')
@section('content')
<div class="page-heading compact"><div><div class="eyebrow">NEW PROJECT</div><h1>Set the work in motion</h1></div></div>
<form method="POST" action="{{ route('projects.store') }}" class="panel form-panel">@csrf @include('projects._form', ['submit' => 'Create project'])</form>
@endsection
