@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __($exception->getMessage() ?: 'Server sedang dalam perbaikan atau kelebihan beban.'))
@section('emoji', '🛠️')
