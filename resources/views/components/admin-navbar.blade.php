<nav class="navbar navbar-expand-md navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand" style="font-size: 1.5rem;"><i class="bi-compass" ></i> ZPK</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                @foreach (config('admin-navbar') as $item)
                <a class="nav-link {{ Request::is(trim($item['url'], '/')) ? 'active' : '' }}" href="{{ url($item['url']) }}">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['name'] }}
                </a>
                @endforeach
            </div>
            <div class="navbar-nav ms-auto">
                <form id="logoutForm">@csrf<button class="nav-link" id="logout-btn" type="submit"><i class="bi-person-circle"></i> Wyloguj</button></form>
                
            </div>
        </div>
    </div>
</nav>