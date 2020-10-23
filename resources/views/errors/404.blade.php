@extends('errors.layout')

@section( 'title', __('Not Found') )
@section( 'message', __('Not Found') )
@section( 'image', '/images/illustrations/not_found.svg')
@section( 'link', secure_url('/home') )
