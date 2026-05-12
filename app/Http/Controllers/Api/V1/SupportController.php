<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\SupportTicketMail;

class SupportController extends Controller
{
    /**
     * Handle incoming support ticket submissions.
     */
    public function submitTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ticketData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'category' => $request->input('category'),
                'subject' => $request->input('subject'),
                'message' => $request->input('message'),
            ];

            // In production, this would go to support@civicseva.com
            // We use config('mail.from.address') or a hardcoded support email
            $supportEmail = env('MAIL_FROM_ADDRESS', 'support@civicseva.com');

            // 1. Send the actual query to the Support Team
            Mail::to($supportEmail)->send(new SupportTicketMail($ticketData));

            // 2. Send the automated confirmation back to the user
            Mail::to($ticketData['email'])->send(new \App\Mail\SupportTicketConfirmationMail($ticketData['name']));

            return response()->json([
                'status' => 'success',
                'message' => 'Your support ticket has been submitted successfully. We will get back to you soon.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send support ticket. Please try again later.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
}
