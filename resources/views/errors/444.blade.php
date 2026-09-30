@extends('errors.layout')

@section('title', __('interface.error-444-title'))

@section('description', $exception->getMessage() ?: __('interface.error-444-description'))
