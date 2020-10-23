@extends('errors.layout')

@section( 'title', __('Service Unavailable') )
@section( 'message', __('Service Unavailable') )
@section( 'image', '/images/illustrations/service_down.svg')
@section( 'link', secure_url('/home') )
