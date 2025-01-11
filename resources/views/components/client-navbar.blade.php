<nav class="navbar navbar-expand-md navbar bg-success-subtle">
    <div class="container-fluid">
        <span class="navbar-brand" style="font-size: 1.5rem;"><i class="bi-compass"></i> ZPK</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                @foreach (config('client-navbar') as $item)
                <a class="nav-link {{ Request::is(trim($item['url'], '/')) || (Request::is('/') && $item['url'] == '/') ? 'active' : '' }}" href="{{ url($item['url']) }}">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['name'] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>