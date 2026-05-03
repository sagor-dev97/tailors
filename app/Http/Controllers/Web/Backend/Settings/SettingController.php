<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SmsSetting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the system settings page.
     *
     * @return View
     */
    public function index(): View
    {
        $setting = Setting::latest('id')->first();
        return view('backend.layouts.settings.general_settings', compact('setting'));
    }
    public function smsConfiguration(): View
    {
        $smsSetting = SmsSetting::first();
        return view('backend.layouts.settings.sms_configuration', compact('smsSetting'));
    }

    // public function updateKey(Request $request)
    // {
    //     $request->validate([
    //         'api_key'   => 'nullable|string|max:255',
    //         'sender_id' => 'nullable|string|max:255',
    //         'sender'    => 'nullable|string|max:255',
    //         'sms_text'   => 'nullable|string|max:500',
    //         'type'       => 'nullable|string|max:50',
    //         'sms_format'   => 'nullable|string|max:50',
    //     ]);

    //     $sms = SmsSetting::first();

    //     // jodi table empty thake tahole new create hobe
    //     if (!$sms) {
    //         $sms = new SmsSetting();
    //     }

    //     $sms->api_key   = $request->api_key;
    //     $sms->sender_id = $request->sender_id;
    //     $sms->sender    = $request->sender;
    //     $sms->sms_text   = $request->sms_text;
    //     $sms->type       = $request->type;
    //     $sms->sms_format   = $request->sms_format;
    //     $sms->save();

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Configuration Updated Successfully',
    //         'data'    => $sms
    //     ]);
    // }

    // public function updateKey(Request $request)
    // {
    //     $request->validate([
    //         'api_key'     => 'nullable|string|max:255',
    //         'sender_id'   => 'nullable|string|max:255',
    //         'sender'      => 'nullable|string|max:255',
    //         'sms_text'    => 'nullable|string|max:500',
    //         'type'        => 'nullable|string|max:50',
    //         'sms_format'  => 'nullable|string|max:50',
    //     ]);

    //     $sms = SmsSetting::first();

    //     if (!$sms) {
    //         $sms = new SmsSetting();
    //     }

    //     // =========================
    //     // UPDATE DATABASE
    //     // =========================
    //     $sms->api_key     = $request->api_key;
    //     $sms->sender_id   = $request->sender_id;
    //     $sms->sender      = $request->sender;
    //     $sms->sms_text    = $request->sms_text;
    //     $sms->type        = $request->type;
    //     $sms->sms_format  = $request->sms_format;
    //     $sms->save();


    //     // =========================
    //     // UPDATE .ENV FILE
    //     // =========================
    //     $this->updateEnv([
    //         'SMS_API_KEY'   => $request->api_key,
    //         'SMS_SENDER_ID' => $request->sender_id,
    //     ]);


    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Configuration Updated Successfully',
    //         'data'    => $sms
    //     ]);
    // }

    // public function updateKey(Request $request)
    // {
    //     $request->validate([
    //         'api_key'     => 'nullable|string|max:255',
    //         'sender_id'   => 'nullable|string|max:255',
    //         'sender'      => 'nullable|string|max:255',
    //         'sms_text'    => 'nullable|string|max:500',  // ডিফল্ট টেমপ্লেট
    //         'type'        => 'nullable|string|max:50',
    //         'sms_format'  => 'nullable|string|max:50',

    //         // টেমপ্লেট গুলো
    //         'pending_template'     => 'nullable|string',
    //         'processing_template'  => 'nullable|string',
    //         'shipped_template'     => 'nullable|string',
    //         'delivered_template'   => 'nullable|string',
    //         'completed_template'   => 'nullable|string',
    //         'cancelled_template'   => 'nullable|string',
    //     ]);

    //     $sms = SmsSetting::first();

    //     if (!$sms) {
    //         $sms = new SmsSetting();
    //     }

    //     // =========================
    //     // বেসিক ফিল্ড আপডেট
    //     // =========================
    //     $sms->api_key     = $request->api_key;
    //     $sms->sender_id   = $request->sender_id;
    //     $sms->sender      = $request->sender;
    //     $sms->type        = $request->type;
    //     $sms->sms_format  = $request->sms_format;

    //     // =========================
    //     // টেমপ্লেট গুলো JSON ফিল্ডে সেভ
    //     // =========================
    //     $templates = [
    //         'pending'    => $request->pending_template,
    //         'processing' => $request->processing_template,
    //         'shipped'    => $request->shipped_template,
    //         'delivered'  => $request->delivered_template,
    //         'completed'  => $request->completed_template,
    //         'cancelled'  => $request->cancelled_template,
    //         'default'    => $request->sms_text  // ডিফল্ট টেমপ্লেট
    //     ];

    //     $sms->templates_json = $templates;  // শুধু JSON ফিল্ডে সেভ হবে

    //     $sms->save();

    //     // =========================
    //     // UPDATE .ENV FILE
    //     // =========================
    //     $this->updateEnv([
    //         'SMS_API_KEY'   => $request->api_key,
    //         'SMS_SENDER_ID' => $request->sender_id,
    //     ]);

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Configuration Updated Successfully',
    //         'data'    => $sms
    //     ]);
    // }

    public function updateKey(Request $request)
{
    $request->validate([
        'api_key'       => 'nullable|string|max:255',
        'sender_id'     => 'nullable|string|max:255',
        'sender'        => 'nullable|string|max:255',
        'type'          => 'nullable|string|max:50',
        'sms_format'    => 'nullable|string|max:50',
        'templates_json' => 'nullable|array',  // Changed from 'templates' to 'templates_json'
    ]);

    $sms = SmsSetting::first();

    if (!$sms) {
        $sms = new SmsSetting();
    }

    // Basic fields update
    $sms->api_key     = $request->api_key;
    $sms->sender_id   = $request->sender_id;
    $sms->sender      = $request->sender;
    $sms->type        = $request->type;
    $sms->sms_format  = $request->sms_format;

    // Template update - FIXED: Use templates_json from request
    if ($request->has('templates_json')) {
        $sms->templates_json = $request->templates_json;
    } else {
        // Fallback for old format compatibility
        $sms->templates_json = [
            'pending'    => $request->pending_template ?? $sms->templates_json['pending'] ?? null,
            'processing' => $request->processing_template ?? $sms->templates_json['processing'] ?? null,
            'shipped'    => $request->shipped_template ?? $sms->templates_json['shipped'] ?? null,
            'delivered'  => $request->delivered_template ?? $sms->templates_json['delivered'] ?? null,
            'completed'  => $request->completed_template ?? $sms->templates_json['completed'] ?? null,
            'cancelled'  => $request->cancelled_template ?? $sms->templates_json['cancelled'] ?? null,
            'default'    => $request->sms_text ?? $request->default_template ?? $sms->templates_json['default'] ?? null,
        ];
    }

    // Remove empty values
    $sms->templates_json = array_filter($sms->templates_json, function ($value) {
        return $value !== null && $value !== '';
    });

    $sms->save();

    // Update .env file
    if ($request->filled('api_key') || $request->filled('sender_id')) {
        $this->updateEnv([
            'SMS_API_KEY'   => $request->api_key,
            'SMS_SENDER_ID' => $request->sender_id,
        ]);
    }

    return response()->json([
        'status'  => true,
        'message' => 'Configuration Updated Successfully',
        'data'    => [
            'basic' => $sms->only(['api_key', 'sender_id', 'sender', 'type', 'sms_format']),
            'templates' => $sms->templates_json
        ]
    ]);
}
    private function updateEnv($data = [])
    {
        $envPath = base_path('.env');

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {

            $oldValue = env($key);

            if (strpos($content, $key . '=') !== false) {
                $content = preg_replace(
                    "/^{$key}=.*$/m",
                    $key . '=' . '"' . $value . '"',
                    $content
                );
            } else {
                $content .= "\n{$key}=\"" . $value . "\"";
            }
        }

        file_put_contents($envPath, $content);
    }


    public function toggleService(Request $request)
    {
        $sms = SmsSetting::first();

        $sms->service_status = $request->status;
        $sms->save();

        return response()->json([
            'status' => true,
            'message' => 'SMS Service Updated Successfully'
        ]);
    }

    public function updateTemplates(Request $request)
    {
        try {
            $request->validate([
                'templates' => 'required|array'
            ]);

            $settings = SmsSetting::first();

            if (!$settings) {
                $settings = new SmsSetting();
            }

            // সরাসরি JSON আপডেট
            $settings->templates_json = $request->templates;
            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Templates updated successfully',
                'data' => $settings->templates_json
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Update the system settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name'           => 'nullable|string|max:50',
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:500',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|string|email|max:100',
            'copyright'      => 'nullable|string|max:255',
            'keywords'       => 'nullable|string|max:255',
            'author'         => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:255',
            'favicon'        => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'thumbnail'      => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        try {
            $setting = Setting::first();

            if ($request->hasFile('favicon')) {
                if ($setting && $setting->favicon && file_exists(public_path($setting->favicon))) {
                    Helper::fileDelete(public_path($setting->favicon));
                }
                $validatedData['favicon'] = Helper::fileUpload($request->file('favicon'), 'settings', time() . '_' . getFileName($request->file('favicon')));
            }

            if ($request->hasFile('thumbnail')) {
                if ($setting && $setting->thumbnail && file_exists(public_path($setting->thumbnail))) {
                    Helper::fileDelete(public_path($setting->thumbnail));
                }
                $validatedData['thumbnail'] = Helper::fileUpload($request->file('thumbnail'), 'settings', time() . '_' . getFileName($request->file('thumbnail')));
            }

            Setting::updateOrCreate(['id' => 1], $validatedData);
            return back()->with('t-success', 'Updated successfully');
        } catch (Exception $e) {
            return back()->with('t-error', 'Failed to update' . $e->getMessage());
        }
    }
}
