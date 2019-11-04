@extends('layouts.app')

@section('title', 'Under Construction')

@push('css')
@endpush

@push('scripts')
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-8">
            <div class="card mb-2">
                <div class="card-body text-center">
                    <div class="">
                        <i class="fas fa-hard-hat fa-5x text-primary"></i>
                    </div>

                    <h3 class="text-dark mt-2 mb-4">
                        {{ __('Under Construction') }}<br>
                        <small class="text-muted">
                            {{ __("We're still working here...check back soon!") }}
                        </small>
                    </h3>

                    <a href="/home" class="btn btn-primary">
                        {{ __('Go home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
