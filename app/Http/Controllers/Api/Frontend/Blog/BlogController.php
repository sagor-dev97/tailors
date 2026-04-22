<?php

namespace App\Http\Controllers\Api\Frontend\Blog;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'category'    => 'required|string|max:255',
            'status'      => 'nullable|in:active,inactive',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);
        try {
            $validatedData['slug'] = Str::slug($request->title . '-' . time());

            if ($request->hasFile('image')) {
                $validatedData['image'] = Helper::fileUpload(
                    $request->file('image'),
                    'blog/image',
                    getFileName($request->file('image'))
                );
            }
            $blog = Blog::create($validatedData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Gallery created successfully',
                'data'    => $blog
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function blogs()
    {
        $blog = Blog::latest()->get();

        if ($blog->isEmpty()) {
            return response()->json([
                'status' => false,
                'code'   => 200,
                'data'   => 'Blogs not found'
            ]);
        }

        $blog->transform(function ($item) {
            $item->image = $item->image ? asset($item->image) : null;
            return $item;
        });

        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => $blog
        ]);
    }

    public function deleteBlogs($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'status' => false,
                'code'   => 404,
                'data'   => 'Blogs not found'
            ]);
        }
        $blog->delete();
        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => 'Blogs deleted successfully'
        ]);
    }

    public function updateBlogs(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'category'    => 'required|string|max:255',
            'status'      => 'nullable|in:active,inactive',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        try {

            DB::beginTransaction();

            $blog = Blog::find($id);


            if (!$blog) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Blog not found'
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
                    'blog/image',
                    getFileName($request->file('image'))
                );
            }

            $blog->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Blog updated successfully',
                'data'    => $blog
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
