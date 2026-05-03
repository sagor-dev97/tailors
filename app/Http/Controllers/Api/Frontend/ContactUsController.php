<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    public function contactSubmit(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'code'    => 422,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Save to Database
            $contact = Contact::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'status'  => 'active',
            ]);

            // Send Mail
            // Mail::raw(
            //     "New Contact Message\n\n" .
            //     "Name: {$request->name}\n" .
            //     "Email: {$request->email}\n" .
            //     "Subject: {$request->subject}\n\n" .
            //     "Message:\n{$request->message}",
            //     function ($mail) use ($request) {
            //         $mail->to('yourmail@gmail.com') // change admin email
            //              ->subject('New Contact Form Submission');
            //     }
            // );

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Contact submitted successfully',
                'data'    => $contact,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

