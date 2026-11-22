@extends('errors.layout')

@section('title', 'Error 403: Forbidden!')

@section('description', $exception->getMessage() ?: 'You do not have permission to perform this action!')
