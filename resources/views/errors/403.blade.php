@extends('errors.layout')

@section( 'title', __('Forbidden') )
@section( 'message', __('Forbidden') )
@section( 'image', '/images/illustrations/happy_team.svg')
@section( 'link', secure_url('/home') )
