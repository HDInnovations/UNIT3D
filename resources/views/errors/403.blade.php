@extends('errors.layout')

@section('title', __('interface.error-403-title'))

@section('description', $exception->getMessage() ?: __('interface.error-403-description'))
