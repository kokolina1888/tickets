@extends('layouts.master')

@section('content')
<header>
    <nav class="navbar p-xs-y-3">
        <div class="container">
            <div class="navbar-content">
                <div>
                    <img src="/img/logo.svg" alt="TicketBeast" style="height: 2.5rem;">
                </div>
                <div>
                    <form class="inline-block" action="{{ route('auth.logout') }}" method="POST">
                        {{ csrf_field() }}
                        <button type="submit" class="link link-light">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</header>
<div class="bg-light p-xs-y-4 border-b">
    <div class="container">
        <div class="flex-spaced flex-y-center">
            <h1 class="text-lg">Your concerts</h1>
            <a href="{{ route('backstage.concerts.new') }}" class="btn btn-primary">Add concert</a>
        </div>
    </div>
</div>
<div class="bg-soft p-xs-y-5">
    <div class="container m-xs-b-4">
        <div class="row">
            @foreach ($concerts as $concert)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-section">
                        <div class="m-xs-b-4">
                            <div class="m-xs-b-2">
                                <h1 class="text-lg wt-bold">{{ $concert->title }}</h1>
                                <p class="wt-medium text-dark-soft">{{ $concert->subtitle }}</p>
                            </div>
                            <p class="text-sm m-xs-b-2">
                                @icon('location', 'zondicon-sm text-dark-soft m-xs-r-1')
                                {{ $concert->venue }} &ndash; {{ $concert->city }}, {{ $concert->state }}
                            </p>
                            <p class="text-sm">
                                @icon('calendar', 'zondicon-sm text-dark-soft m-xs-r-1')
                                {{ $concert->formatted_date }} @ {{ $concert->formatted_start_time }}
                            </p>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('backstage.concerts.edit', $concert->id) }}" class="btn btn-secondary btn-block">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<footer class="p-xs-y-6 text-light-muted">
    <div class="container">
        <p class="text-center">&copy; TicketBeast {{ date('Y') }}</p>
    </div>
</footer>
@endsection
