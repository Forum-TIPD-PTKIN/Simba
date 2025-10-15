@extends('errors::minimal')

@section('title', __('Unprocessable Entity'))
@section('code', '422')
@section('message', __($exception->getMessage() ?: 'Terdapat kesalahan validasi pada data yang Anda kirim.'))
@section('emoji', '📝')
