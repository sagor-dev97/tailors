<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Helpers\Helper;
use App\Models\CMS;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $cmsItems = CMS::query()
            ->where('page', PageEnum::HOME)
            ->where('status', 'active')
            ->whereIn('section', [SectionEnum::EXAMPLE, SectionEnum::ABOUT])
            ->get();

        $homeIntro  = $cmsItems->where('section', SectionEnum::EXAMPLE)->first();
        $homeBanner = $cmsItems->where('section', SectionEnum::ABOUT)->first();

        $data = [
            'about_header' => $homeIntro ? [
                'title'       => $homeIntro->title,
                'sub_title'   => $homeIntro->sub_title,
                'description' => strip_tags($homeIntro->description),
            ] : null,

            'about_section' => $homeBanner ? [
                'title'       => $homeBanner->title,
                'sub_title'   => $homeBanner->sub_title,
                'description' => strip_tags($homeBanner->description),
            ] : null,
        ];

        return Helper::jsonResponse(true, 'Home Page', 200, $data);
    }

    public function ourResponsibility()
    {
        $data = [];

        // Fetch only EXAMPLES section
        $heroBanner = CMS::query()
            ->where('page', PageEnum::HOME)
            ->where('status', 'active')
            ->where('section', SectionEnum::EXAMPLES)
            ->first();

        $data['hero_banner'] = $heroBanner ? [
            'title'       => $heroBanner->title,
            'sub_title'   => $heroBanner->sub_title,
            'description' => strip_tags($heroBanner->description),
            'image'       => $heroBanner->image
                ? url($heroBanner->image)
                : null,
        ] : null;

        return Helper::jsonResponse(true, 'Our Responsibility Data', 200, $data);
    }
    // How it works

    public function howItWorks()
    {
        $data = [];

        // Fetch all relevant CMS items
        $cmsItems = CMS::query()
            ->where('page', PageEnum::HOWITWORKS)
            ->where('status', 'active')
            ->whereIn('section', [
                SectionEnum::HEROBANNER,
                'simple-selling',   // single section header
                'simple-sellings',  // list of items
                SectionEnum::SAFELYSHOPS,
                'safely-shop',
                'safely-shops'
            ])
            ->orderBy('id')
            ->get();

        // Hero banner
        $data['hero_banner'] = $cmsItems->where('section', SectionEnum::HEROBANNER)->first();

        /*
     |--------------------------
     | Simple Selling Section
     |--------------------------
    */

        // Get section heading (title, sub_title) from `simple-selling`
        $sectionHeader = $cmsItems->where('section', 'simple-selling')->first();
        $sectionTitle = $sectionHeader->title ?? null;
        $sectionSubTitle = $sectionHeader->sub_title ?? null;

        // Get all list items from `simple-sellings`
        $simpleSellingItems = $cmsItems->where('section', 'simple-sellings')->values()->toArray();

        foreach ($simpleSellingItems as $key => &$item) {
            $item['index'] = $key + 1;
        }
        unset($item);

        $data['simple_selling'] = [
            'title' => $sectionTitle,
            'sub_title' => $sectionSubTitle,
            'items' => $simpleSellingItems
        ];

        /*
     |--------------------------
     | Safely Shop Section
     |--------------------------
    */

        $SelfsectionHeader = $cmsItems->where('section', 'safely-shop')->first();
        $SelfsectionTitle = $SelfsectionHeader->title ?? null;
        $SelfsectionSubTitle = $SelfsectionHeader->sub_title ?? null;

        $safelyShopItems = $cmsItems->where('section', SectionEnum::SAFELYSHOPS)->values()->toArray();

        foreach ($safelyShopItems as $key => &$item) {
            $item['index'] = $key + 1;
        }
        unset($item);

        $data['self_selling'] = [
            'title' => $SelfsectionTitle,
            'sub_title' => $SelfsectionSubTitle,
            'items' => $safelyShopItems
        ];



        return Helper::jsonResponse(true, 'How It Works', 200, $data);
    }


    public function howItWorksDetails($slug)
    {
        // Find the CMS item by slug
        $item = CMS::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->whereIn('section', ['simple-sellings', 'safely-shops'])
            ->first();

        if (!$item) {
            return Helper::jsonResponse(false, 'Item not found', 404);
        }

        // Find index in its section
        $allItems = CMS::query()
            ->where('page', PageEnum::HOWITWORKS)
            ->where('status', 'active')
            ->where('section', $item->section)
            ->orderBy('id')
            ->get()
            ->values();

        foreach ($allItems as $key => $sectionItem) {
            if ($sectionItem->slug === $slug) {
                $item->index = $key + 1;
                break;
            }
        }

        // Determine section header slug based on item section
        if ($item->section === 'simple-sellings') {
            $sectionHeaderSlug = 'simple-selling';
        } elseif ($item->section === 'safely-shops') {
            $sectionHeaderSlug = 'safely-shop';
        } else {
            $sectionHeaderSlug = null;
        }

        // Fetch the section header if it exists
        $sectionTitle = null;
        $sectionSubTitle = null;
        if ($sectionHeaderSlug) {
            $sectionHeader = CMS::query()
                ->where('page', PageEnum::HOWITWORKS)
                ->where('section', $sectionHeaderSlug)
                ->where('status', 'active')
                ->first();

            if ($sectionHeader) {
                $sectionTitle = $sectionHeader->title ?? null;
                $sectionSubTitle = $sectionHeader->sub_title ?? null;
            }
        }

        // Prepare the data to return, excluding section_title and section_sub_title inside data
        $responseData = $item->toArray();
        unset($responseData['section_title'], $responseData['section_sub_title']);

        // Build final response with section_title and section_sub_title on top-level
        return response()->json([
            'status' => true,
            'message' => 'Item details fetched successfully',
            'code' => 200,
            'section_title' => $sectionTitle,
            'section_sub_title' => $sectionSubTitle,
            'data' => $responseData,
        ]);
    }
}
