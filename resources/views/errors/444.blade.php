@extends('errors.layout')

@section('title', 'Error 444')

@section('description', $exception->getMessage() ?: 'CONNECTION CLOSED WITHOUT RESPONSE.')
