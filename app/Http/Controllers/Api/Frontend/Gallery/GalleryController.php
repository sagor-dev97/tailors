<?php

namespace App\Http\Controllers\Api\Frontend\Gallery;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|string|max:255',
            'status'      => 'nullable|in:active,inactive',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);
        try {
            $validatedData['slug'] = Str::slug($request->title . '-' . time());

            if ($request->hasFile('image')) {
                $validatedData['image'] = Helper::fileUpload(
                    $request->file('image'),
                    'gallery/image',
                    getFileName($request->file('image'))
                );
            }
            $gallery = Gallery::create($validatedData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Gallery created successfully',
                'data'    => $gallery
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getGallery()
    {
        $gallery = Gallery::latest()->get();

        if ($gallery->isEmpty()) {
            return response()->json([
                'status' => false,
                'code'   => 200,
                'data'   => 'Gallery image not found'
            ]);
        }

        $gallery->transform(function ($item) {
            $item->image = $item->image ? asset($item->image) : null;
            return $item;
        });

        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => $gallery
        ]);
    }

    public function deleteGallery($id)
    {
        $gallery = Gallery::find($id);
        if (!$gallery) {
            return response()->json([
                'status' => false,
                'code'   => 404,
                'data'   => 'Gallery image not found'
            ]);
        }
        $gallery->delete();
        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => 'Gallery image deleted successfully'
        ]);
    }

    public function updateGallery(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|string|max:255',
            'status'      => 'nullable|in:active,inactive',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        try {

            DB::beginTransaction();

            $gallery = Gallery::find($id);


            if (!$gallery) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gallery not found'
                ], 404);
            }

            // Update slug
            $validatedData['slug'] = Str::slug($request->title . '-' . time());

            // Image update
            if ($request->hasFile('image')) {

                // delete old image
                if (!empty($gallery->image)) {
                    Helper::fileDelete(public_path($gallery->image));
                }

                $validatedData['image'] = Helper::fileUpload(
                    $request->file('image'),
                    'gallery/image',
                    getFileName($request->file('image'))
                );
            }

            $gallery->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Gallery updated successfully',
                'data'    => $gallery
            ], 200);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
