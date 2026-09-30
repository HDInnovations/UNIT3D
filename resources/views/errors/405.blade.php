@extends('errors.layout')

@section('title', __('interface.error-405-title'))

@section('description', $exception->getMessage() ?: __('interface.error-405-description'))
