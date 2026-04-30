<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorBankDetail;
use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorRegistrationController extends Controller
{
    /**
     * Show the vendor registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('vendor-registration.register');
    }

    /**
     * Process the vendor registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:individual,company',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create vendor
            $vendor = Vendor::create([
                'name' => $request->name,
                'type' => $request->type,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'pan_number' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'pincode' => '',
                'nature_of_work' => '',
                'status' => 'pending_verification',
            ]);

            // Create vendor user account
            $user = \App\Models\User::create([
                'name' => $request->contact_person,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
            ]);

            // Generate verification token
            $token = Str::random(64);
            \DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            // Send verification email
            Mail::send('emails.vendor-verification', ['token' => $token, 'vendor' => $vendor], function($message) use ($request) {
                $message->to($request->email);
                $message->subject('Verify Your Vendor Account');
            });

            DB::commit();

            return redirect()->route('vendor.register.success')
                ->with('success', 'Registration successful! Please check your email to verify your account.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error during registration: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show registration success page.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationSuccess()
    {
        return view('vendor-registration.success');
    }

    /**
     * Verify vendor email.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function verifyEmail($token)
    {
        $tokenData = \DB::table('password_resets')->where('token', $token)->first();

        if (!$tokenData) {
            return redirect()->route('vendor.login')
                ->with('error', 'Invalid verification token.');
        }

        $vendor = Vendor::where('email', $tokenData->email)->first();

        if (!$vendor) {
            return redirect()->route('vendor.login')
                ->with('error', 'Vendor not found.');
        }

        // Show the profile completion form
        return view('vendor-registration.complete-profile', compact('vendor', 'token'));
    }

    /**
     * Complete vendor profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completeProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'token' => 'required|string',
            'pan_number' => 'required|string|unique:vendors,pan_number',
            'gst_number' => 'nullable|string|unique:vendors,gst_number',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'nature_of_work' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'upi_id' => 'nullable|string|max:50',
            'pan_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'gst_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'cheque_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'website' => 'nullable|url|max:255',
            'msme_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:20',
            'contacts.*.designation' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify token
        $tokenData = \DB::table('password_resets')->where('token', $request->token)->first();

        if (!$tokenData) {
            return redirect()->back()
                ->with('error', 'Invalid verification token.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $vendor = Vendor::findOrFail($request->vendor_id);

            // Update vendor
            // Handle MSME certificate upload
            $msmePath = null;
            if ($request->hasFile('msme_certificate')) {
                $msmeFile = $request->file('msme_certificate');
                $msmePath = $msmeFile->store('vendor_documents', 'public');
            }

            $vendor->update([
                'pan_number' => $request->pan_number,
                'gst_number' => $request->gst_number,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'nature_of_work' => $request->nature_of_work,
                'website' => $request->website,
                'msme_certificate_path' => $msmePath,
            ]);

            // Create bank details
            VendorBankDetail::create([
                'vendor_id' => $vendor->id,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
                'is_primary' => true,
            ]);

            // Store PAN document
            if ($request->hasFile('pan_document')) {
                $panFile = $request->file('pan_document');
                $panPath = $panFile->store('vendor_documents', 'public');
                
                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'pan',
                    'file_path' => $panPath,
                    'file_name' => $panFile->getClientOriginalName(),
                ]);
            }

            // Store GST document
            if ($request->hasFile('gst_document')) {
                $gstFile = $request->file('gst_document');
                $gstPath = $gstFile->store('vendor_documents', 'public');
                
                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'gst',
                    'file_path' => $gstPath,
                    'file_name' => $gstFile->getClientOriginalName(),
                ]);
            }

            // Store cancelled cheque document
            if ($request->hasFile('cheque_document')) {
                $chequeFile = $request->file('cheque_document');
                $chequePath = $chequeFile->store('vendor_documents', 'public');
                
                VendorDocument::create([
                    'vendor_id' => $vendor->id,
                    'document_type' => 'cancelled_cheque',
                    'file_path' => $chequePath,
                    'file_name' => $chequeFile->getClientOriginalName(),
                ]);
            }

            // Create vendor contacts
            if ($request->has('contacts')) {
                foreach ($request->contacts as $contactData) {
                    $vendor->contacts()->create($contactData);
                }
            }

            // Delete token
            \DB::table('password_resets')->where('token', $request->token)->delete();

            // Notify admin about new vendor registration
            $admins = \App\Models\User::where('role', 'admin')->orWhere('role', 'hr')->get();
            foreach ($admins as $admin) {
                Mail::send('emails.new-vendor-notification', ['vendor' => $vendor], function($message) use ($admin) {
                    $message->to($admin->email);
                    $message->subject('New Vendor Registration');
                });
            }

            DB::commit();

            return redirect()->route('vendor.login')
                ->with('success', 'Profile completed successfully. Please wait for admin approval before logging in.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error completing profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
