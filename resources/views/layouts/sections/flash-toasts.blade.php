<!-- Global Toastr setup for Laravel Sessions -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if (session('success'))
            toastr.success("{{ session('success') }}", 'Succès', {
                positionClass: 'toast-top-center',
                timeOut: 3000,
                progressBar: true
            });
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}", 'Erreur', {
                positionClass: 'toast-top-center',
                timeOut: 5000,
                progressBar: true
            });
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}", 'Attention', {
                positionClass: 'toast-top-center',
                timeOut: 4000,
                progressBar: true
            });
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}", 'Information', {
                positionClass: 'toast-top-center',
                timeOut: 3000,
                progressBar: true
            });
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}", 'Erreur de Validation', {
                    positionClass: 'toast-top-center',
                    timeOut: 6000,
                    progressBar: true
                });
            @endforeach
        @endif
    });
</script>
