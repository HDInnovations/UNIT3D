@extends('errors.layout')

@section('title', 'Error 401: Unauthorized!')

@section('description', $exception->getMessage() ?: 'Error code response for missing or invalid authentication token.')
