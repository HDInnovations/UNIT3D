@extends('errors.layout')

@section('title', __('interface.error-404-title'))

@section('description', $exception->getMessage() ?: __('interface.error-404-description'))
