@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __($exception->getMessage() ?: 'Sesi Anda telah berakhir. Silakan muat ulang halaman.'))
@section('emoji', '⏳')
