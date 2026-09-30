@extends('errors.layout')

@section('title', __('interface.error-502-title'))

@section('description', $exception->getMessage() ?: __('interface.error-502-description'))
