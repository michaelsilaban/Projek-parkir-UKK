        </main>
    </div>
</div>
<script>
// Sidebar responsif: di layar kecil sidebar menjadi menu geser (off-canvas).
(function () {
    var sidebar = document.getElementById('fiSidebar');
    var overlay = document.getElementById('fiOverlay');
    var toggle  = document.getElementById('fiMenuToggle');
    if (!sidebar || !overlay || !toggle) return;

    function setOpen(open) {
        sidebar.classList.toggle('is-open', open);
        overlay.classList.toggle('is-visible', open);
        document.body.classList.toggle('fi-nav-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
    }

    toggle.addEventListener('click', function () {
        setOpen(!sidebar.classList.contains('is-open'));
    });
    overlay.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
    });
    sidebar.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () { setOpen(false); });
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) setOpen(false);
    });
})();
</script>
</body>
</html>
