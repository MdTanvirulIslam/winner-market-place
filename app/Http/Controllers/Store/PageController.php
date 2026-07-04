<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function refundPolicy(): View
    {
        return view('pages.refund-policy');
    }

    public function contact(): View
    {
        return view('pages.contact', ['setting' => Setting::current()]);
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'website' => 'prohibited', // honeypot — bots fill every field
        ]);

        $to = Setting::current()->support_email ?: config('mail.from.address');

        try {
            Mail::to($to)->send(new ContactMessageMail(
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message'],
            ));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'Sorry, your message could not be sent right now — please try again later.');
        }

        return redirect()->route('store.contact')
            ->with('success', "Thanks! We received your message and will reply to {$data['email']} soon.");
    }
}
