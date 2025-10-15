@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __($exception->getMessage() ?: 'Halaman yang kamu cari tidak tersedia.'))
@section('emoji', '🔍')
