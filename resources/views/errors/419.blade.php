@extends('errors.layout')

@section( 'title', __('Page Expired') )
@section( 'message', __('Page Expired') )
@section( 'image', '/images/illustrations/problems.svg')
@section( 'link', secure_url('/home') )
