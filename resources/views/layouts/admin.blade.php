<!DOCTYPE html>
<html lang="pl">
    <x-head />
    <body>
        {{-- <x-background /> --}}
        <x-admin-navbar />

        <div class="content">
            <div class="container-md bg-light">
                @yield('content')
            </div>
        </div>

        <x-scripts />
    </body>
</html>