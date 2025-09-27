<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
        <h3>Redirecting to notification...</h3>
        <p>Please wait while we process your request.</p>

        <form id="redirectForm" method="POST" action="{{ route('landlord.notifications.read', $notification) }}">
            @csrf
            <noscript>
                <button type="submit" style="background: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                    Continue
                </button>
            </noscript>
        </form>
    </div>

    <script>
        // Auto-submit the form immediately
        document.getElementById('redirectForm').submit();
    </script>
</body>
</html>