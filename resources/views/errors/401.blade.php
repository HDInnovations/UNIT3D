@extends('errors.layout')

@section('title', __('interface.error-401-title'))

@section('description', $exception->getMessage() ?: __('interface.error-401-description'))
