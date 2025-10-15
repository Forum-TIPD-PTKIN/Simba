@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Kamu tidak memiliki izin untuk mengakses halaman ini.'))
@section('emoji', '🚫')
