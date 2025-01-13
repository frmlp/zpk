<!DOCTYPE html>
<html lang="pl">
    <x-head />
    <body>
        <x-background />
        <x-client-navbar />

        <div class="content">
            <div class="container-md">
                @yield('content')
            </div>
        </div>

        <x-client-scripts />
    </body>
</html>