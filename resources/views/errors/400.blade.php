@extends('errors::minimal')

@section('title', __('Bad Request'))
@section('code', '400')
@section('message', __($exception->getMessage() ?: 'Permintaan tidak valid atau rusak.'))
@section('emoji', '✋')
