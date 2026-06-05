<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Unavailable</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .icon {
            width: 64px; height: 64px;
            background: #fef9c3;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        h1 { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        p  { font-size: 14px; color: #6b7280; line-height: 1.6; }
        .event-name { font-weight: 600; color: #374151; }
        .status {
            display: inline-block;
            margin-top: 1rem;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            background: #f3f4f6;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">
        <svg style="width:32px;height:32px;color:#ca8a04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
    </div>
    <h1>Check-in not available</h1>
    <p>
        <span class="event-name">{{ $event->title }}</span> is currently
        not open for check-in.
    </p>
    <span class="status">Status: {{ ucfirst($event->status) }}</span>
    <p style="margin-top:1.5rem;font-size:13px;color:#9ca3af;">
        Please ask an usher or contact the church office for assistance.
    </p>
</div>
</body>
</html>
