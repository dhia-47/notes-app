</main> <!-- /container -->
<style>
    html, body {
        height: 100%;
        margin: 0;
    }
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background: #f8f9fa;
    }
    main {
        flex: 1; /* This pushes footer to bottom */
    }

</style>

<footer class="bg-white border-top mt-5 py-3">
    <div class="container text-center">
        <small class="text-muted">
            © <?= date("Y") ?> NotesApp — All Rights Reserved.
        </small>
    </div>
</footer>

<!-- Bootstrap JS bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional custom JS -->
<script src="/notes-app/public/assets/js/app.js"></script>

</body>
</html>
