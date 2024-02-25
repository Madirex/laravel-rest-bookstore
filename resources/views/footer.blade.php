<footer class="mt-4 text-center">
    <hr>
    <p>BookStore<a href="#" target="_blank"></a></p>
    <p>&copy; <?php echo date('Y'); ?> NULLERS. Todos los derechos reservados.</p>
    @if(session()->has('username'))
        Visitas durante esta sesión: {{ session('visits') }}
    @endif
</footer>
