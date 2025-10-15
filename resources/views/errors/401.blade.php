@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __($exception->getMessage() ?: 'Anda belum login atau tidak memiliki akses.'))
@section('emoji', '🔒')
