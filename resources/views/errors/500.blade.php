@extends('errors.layout')

@section( 'title', __('Server Error') )
@section( 'message', __('Server Error') )
@section( 'image', '/images/illustrations/game_world.svg')
@section( 'link', secure_url('/home') )
