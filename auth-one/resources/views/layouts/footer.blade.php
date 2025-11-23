<footer class="main-footer bg-light text-dark text-center py-3">
    <strong>Copyright &copy; 2014-{{ date('Y') }} </strong> <br>
    All rights reserved by Rafia Hawlader.
    <!-- <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.2.0
    </div> -->
</footer>

<aside class="control-sidebar control-sidebar-dark">
    </aside>

{{-- 🛑 IMPORTANT: Closing the main wrapper div opened in header.blade.php --}}
</div> 
{{-- 🛑 IMPORTANT: ADMINLTE CORE JAVASCRIPT PLUGINS --}}
<script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('admin/dist/js/adminlte.js')}}"></script>


{{-- ✅ CRITICAL FIX: YIELD CUSTOM SCRIPTS HERE --}}
{{-- This line is essential for loading Chart.js from welcome.blade.php --}}
@yield('scripts')

</body>
</html>