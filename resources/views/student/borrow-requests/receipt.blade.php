<html>
    <head>
        <meta charset="utf-8">
        <title>Borrow Receipt - {{ $borrowRequest->id }}</title>
        <style>
            body { font-family: Arial, sans-serif; color: #111; }
            .container { max-width: 700px; margin: 20px auto; padding: 20px; border: 1px solid #eee; }
            .header { text-align: center; margin-bottom: 20px; }
            .details { margin-top: 10px; }
            .row { display:flex; justify-content:space-between; margin:6px 0; }
            .footer { margin-top: 30px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Library Borrow Receipt</h1>
                <p>Receipt #: {{ $borrowRequest->id }}</p>
            </div>

            <div class="details">
                <h3>Student</h3>
                <div class="row"><div><strong>Name</strong></div><div>{{ $borrowRequest->student->name }}</div></div>
                <div class="row"><div><strong>Student ID</strong></div><div>{{ $borrowRequest->student->id }}</div></div>
                <div class="row"><div><strong>Email</strong></div><div>{{ $borrowRequest->student->email }}</div></div>

                <h3 style="margin-top:18px">Book</h3>
                <div class="row"><div><strong>Title</strong></div><div>{{ $borrowRequest->book->title }}</div></div>
                <div class="row"><div><strong>Author</strong></div><div>{{ $borrowRequest->book->author }}</div></div>
                <div class="row"><div><strong>Borrowed At</strong></div><div>{{ optional($borrowRequest->borrowed_at)->format('M j, Y g:i A') ?? 'N/A' }}</div></div>
                <div class="row"><div><strong>Due At</strong></div><div>{{ optional($borrowRequest->due_at)->format('M j, Y') ?? 'N/A' }}</div></div>
                <div class="row"><div><strong>Handled By</strong></div><div>{{ optional($borrowRequest->handler)->name ?? 'N/A' }}</div></div>

                <div class="row" style="margin-top:18px"><div><strong>Status</strong></div><div>{{ ucfirst($borrowRequest->status) }}</div></div>
            </div>

            <div class="footer">
                <p>Generated: {{ now()->format('M j, Y g:i A') }}</p>
                <p>Thank you for using our library. Please return books on time to avoid late fees.</p>
            </div>
        </div>
    </body>
</html>