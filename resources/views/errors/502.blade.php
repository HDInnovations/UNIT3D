@extends('errors.layout')

@section('title', 'Error 502: Bad gateway!')

@section('description', $exception->getMessage() ?: 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.')
