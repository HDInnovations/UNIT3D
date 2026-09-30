@extends('errors.layout')

@section('title', __('interface.error-400-title'))

@section('description', $exception->getMessage() ?: __('interface.error-400-description'))
