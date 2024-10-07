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
@section('js')
    <script>
        // Session timeout in seconds (example: 10 minutes session)
        var sessionLifetime = {{ config('session.lifetime') * 60 }};
        var warningTime = 30; // Time before expiry to show warning modal
        var timer;

        // Function to show the popup 30 seconds before session ends
        function showSessionExpireModal() {
            $('#sessionExpireModal').modal('show');
            var counter = warningTime;

            timer = setInterval(function() {
                counter--;
                $('#session-expire-counter').text(counter);
                if (counter <= 0) {
                    clearInterval(timer);
                    // Redirect to logout or session expired action if not continued
                    window.location.href = '{{ route('logout') }}';
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
                    clearInterval(timer);
                    startSessionTimer(); // Restart the timer
                }
            });
        });

        // Function to start the session timer
        function startSessionTimer() {
            setTimeout(showSessionExpireModal, (sessionLifetime - warningTime) * 1000);
        }

        // Start the timer when page loads
        $(document).ready(function() {
            startSessionTimer();
        });
    </script>

@stop
