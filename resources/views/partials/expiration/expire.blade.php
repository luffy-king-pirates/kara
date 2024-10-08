<!-- Session Expiry Modal -->

    <div class="modal fade" id="sessionExpireModal" tabindex="-1" role="dialog" aria-labelledby="sessionExpireLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sessionExpireLabel">Session Expiring Soon</h5>
                </div>
                <div class="modal-body">
                    Your session will expire in <span id="session-expire-counter">30</span> seconds. Would you like to
                    continue your session?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="continueSessionBtn">Continue Session</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Logout</button>
                </div>
            </div>
        </div>
    </div>


{{--  --}}
@include('partials.import-cdn')
    <script>

        // Session timeout in seconds (example: 10 minutes session)
        var sessionLifetime = {{ config('session.lifetime') * 60 }};
        var warningTime = 30; // Time before expiry to show warning modal
        var sessionTimer, warningTimer;

        // Function to show the popup 30 seconds before session ends
        function showSessionExpireModal() {
            $('#sessionExpireModal').modal('show');
            var counter = warningTime;

            var countdownTimer = setInterval(function() {
                counter--;
                $('#session-expire-counter').text(counter);
                if (counter <= 0) {
                    clearInterval(countdownTimer);
                    // Redirect to logout or session expired action if not continued
                    window.location.reload();
                }
            }, 1000);
        }

        // Event when the user clicks "Continue Session"
        $('#continueSessionBtn').on('click', function() {
            // Send an AJAX request to renew session
            $.ajax({
                url: '{{ route('renew-session') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function() {
                    $('#sessionExpireModal').modal('hide');
                    clearTimeout(warningTimer);
                    startSessionTimer(); // Restart the timer after session renewal
                },
                error: function() {
                    // Handle failure case, possibly showing an error message
                    window.location.reload(); // Optionally redirect on error
                }
            });
        });

        // Function to start the session timer
        function startSessionTimer() {
            clearTimeout(sessionTimer); // Ensure no duplicate timers
            clearTimeout(warningTimer); // Clear the existing warning timer

            // Start the session expiration countdown, showing the modal at warning time
            sessionTimer = setTimeout(function() {
                showSessionExpireModal(); // Show modal when time is close to expiration
            }, (sessionLifetime - warningTime) * 1000);
        }

        // Start the timer when the page loads
        $(document).ready(function() {
            startSessionTimer(); // Initialize session timeout handling
        });
    </script>

