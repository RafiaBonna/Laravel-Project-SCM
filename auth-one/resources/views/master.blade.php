@include('layouts.header')
@include('layouts.nav')
@include('layouts.Sidebar')
    <div class="content-wrapper">
        @yield('content')
    </div>
@include('layouts.footer')