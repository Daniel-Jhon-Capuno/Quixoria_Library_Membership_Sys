<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\Resolution;
use Illuminate\Http\Request;

class ResolutionController extends Controller
{
    /**
     * Store a new resolution (payment record)
     */
    public function store(Request $request, $borrowRequestId)
    {
        $request->validate([
            'amount_cents' => 'required|integer|min:0',
            'method' => 'required|string|in:cash,gcash,bank',
            'notes' => 'nullable|string|max:500',
        ]);

        $borrow = BorrowRequest::findOrFail($borrowRequestId);

        $receiptNumber = 'RCPT-' . now()->format('YmdHis') . '-' . $borrow->id;

        $resolution = $borrow->resolution()->create([
            'admin_id' => auth()->id(),
            'amount_cents' => $request->amount_cents,
            'method' => $request->method,
            'notes' => $request->notes,
            'receipt_number' => $receiptNumber,
        ]);

        return response()->json([
            'message' => 'Resolution recorded.',
            'receipt_number' => $receiptNumber,
        ]);
    }

    /**
     * Generate and display resolution receipt
     */
    public function receipt($resolutionId)
    {
        $resolution = Resolution::with(['borrowRequest.student', 'borrowRequest.book', 'admin'])->findOrFail($resolutionId);

        return view('admin.resolutions.receipt', compact('resolution'));
    }

    /**
     * Download resolution receipt as PDF
     */
    public function downloadReceipt($resolutionId)
    {
        $resolution = Resolution::with(['borrowRequest.student', 'borrowRequest.book', 'admin'])->findOrFail($resolutionId);

        // For now, return PDF view; in production use a PDF library like DOMPDF
        return view('admin.resolutions.receipt-pdf', compact('resolution'));
    }
}
