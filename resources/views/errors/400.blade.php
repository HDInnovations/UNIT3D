@extends('errors.layout')

@section('title', 'Error 400: Bad request!')

@section('description', $exception->getMessage() ?: 'The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications.')
