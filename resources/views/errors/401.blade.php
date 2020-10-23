@extends('errors.layout')

@section( 'title', __('Unauthorized') )
@section( 'message', __('Unauthorized') )
@section( 'image', '/images/illustrations/reminder.svg')
@section( 'link', secure_url('/home') )
