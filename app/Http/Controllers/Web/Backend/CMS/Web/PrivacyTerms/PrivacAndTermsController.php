<?php

namespace App\Http\Controllers\Web\Backend\CMS\Web\PrivacyTerms;

use Illuminate\Http\Request;
use App\Models\PrivecyAndTerms;
use App\Http\Controllers\Controller;
use App\Models\PrivacyTerms;

class PrivacAndTermsController extends Controller
{
    public function termsAndCondition()
    {
        $terms = PrivacyTerms::where('type', 'terms')->first();
        return view('backend.layouts.privacyandterms.terms_condition', compact('terms'));
    }

    public function termsAndConditionUpdate(Request $request)
    {
        $request->validate([
            'terms_conditions' => 'required',
        ]);

        // Find existing record with type 'terms'
        $terms = PrivacyTerms::where('type', 'terms')->first();

        if ($terms) {
            // Update existing record
            $terms->terms_conditions = $request->terms_conditions;
        } else {
            // Create new record
            $terms = new PrivacyTerms();
            $terms->type = 'terms';
            $terms->terms_conditions = $request->terms_conditions;
        }

        $terms->save();

        return redirect()->back()->with('success', 'Terms and Conditions updated successfully.');
    }


    public function privacyPolicy()
    {
        $privacy = PrivacyTerms::where('type', 'privacy')->first();
        return view('backend.layouts.privacyandterms.privacy_policy', compact('privacy'));
    }

    public function privacyPolicyUpdate(Request $request)
    {
        $request->validate([
            'privacy_policy' => 'required',
        ]);

        // Find existing record with type 'privacy'
        $privacy = PrivacyTerms::where('type', 'privacy')->first();

        if ($privacy) {
            // Update existing record
            $privacy->privacy_policy = $request->privacy_policy;
        } else {
            // Create new record
            $privacy = new PrivacyTerms();
            $privacy->type = 'privacy';
            $privacy->privacy_policy = $request->privacy_policy;
        }

        $privacy->save();

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }

    /**
     * show why desi carouel update page
     */
    public function whyDesiCarousel()
    {
        $why_desi_carousel = PrivacyTerms::where('type', 'why_desi_carousel')->first();
        return view('backend.layouts.privacyandterms.why_desi_carousel', compact('why_desi_carousel'));
    }


    /**
     * update why desi carousel
     */
    public function whyDesiCarouselUpdate(Request $request)
    {
        
        $request->validate([
            'description' => 'required',
        ]);

        // Find existing record with type 'privacy'
        $why_desi_carousel = PrivacyTerms::where('type', 'why_desi_carousel')->first();

        if ($why_desi_carousel) {
            // Update existing record
            $why_desi_carousel->description = $request->description;
        } else {
            // Create new record
            $why_desi_carousel = new PrivacyTerms();
            $why_desi_carousel->type = 'why_desi_carousel';
            $why_desi_carousel->description = $request->description;
        }

        $why_desi_carousel->save();

        return redirect()->back()->with('success', 'Privacy Policy updated successfully.');
    }

    
}
