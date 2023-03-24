<nav class="navbar navbar-expand-sm navbar-light bg-white shadow-sm" role="navigation">
    <div class="container mx-auto">
        @include('layouts.brand')

        <ul class="navbar-nav mr-0">
            <div class="mx-auto">
            @guest
                <li class="nav-item">
                    <a class="nav-link fw-bold" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown ">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="navbarDropdown">

                        <a class="dropdown-item text-dark" href="/account">
                            <i class="fas fa-user-cog text-muted"></i> {{ __('Account') }}
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item text-dark" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                            <i class="fas fa-power-off text-muted"></i> {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
            </div>
        </ul>
    </div>
</nav>

@include('system.maintenance-bar')
