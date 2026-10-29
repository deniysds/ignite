<!-- Scripts -->
<script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/js/core.bundle.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/vendors/ktui/ktui.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/js/layouts/metronic.js') }}" data-navigate-once></script>

<!-- Compiled App Scripts -->
@vite(['resources/js/app.js'])

@auth
<!-- Inactivity Watchdog Timer (5 Minutes Inactivity Logoff - Point 7) -->
<script>
    (function() {
        const idleTimeoutMs = {{ (int) config('usermanagement.session_idle_timeout_seconds', 300) * 1000 }};
        const warningTimeMs = Math.max(10000, idleTimeoutMs - 30000); // 30s before timeout
        let idleTimer = null;
        let warningTimer = null;
        let countdownInterval = null;
        let isWarningVisible = false;

        function resetIdleTimer() {
            if (isWarningVisible) return;
            clearTimeout(idleTimer);
            clearTimeout(warningTimer);

            warningTimer = setTimeout(showInactivityWarning, warningTimeMs);
            idleTimer = setTimeout(performAutoLogout, idleTimeoutMs);
        }

        function showInactivityWarning() {
            isWarningVisible = true;
            let secondsLeft = Math.round((idleTimeoutMs - warningTimeMs) / 1000);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __("usermanagement::app.session_warning_title") }}',
                    html: 'Sesi Anda akan berakhir dalam <strong id="inactivity_seconds">' + secondsLeft + '</strong> detik karena tidak ada aktivitas.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("usermanagement::app.stay_logged_in") }}',
                    cancelButtonText: 'Keluar',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    clearInterval(countdownInterval);
                    isWarningVisible = false;
                    if (result.isConfirmed) {
                        fetch('/up').catch(() => {});
                        resetIdleTimer();
                    } else {
                        performAutoLogout();
                    }
                });

                countdownInterval = setInterval(() => {
                    secondsLeft--;
                    const span = document.getElementById('inactivity_seconds');
                    if (span) span.textContent = secondsLeft;
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        performAutoLogout();
                    }
                }, 1000);
            }
        }

        function performAutoLogout() {
            window.location.href = "{{ route('logout') }}";
        }

        const events = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'];
        events.forEach(event => {
            window.addEventListener(event, resetIdleTimer, { passive: true });
        });

        resetIdleTimer();
    })();
</script>
@endauth
<!-- End of Scripts -->
