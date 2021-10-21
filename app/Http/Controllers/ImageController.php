<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\ImageProcess;

class ImageController extends Controller
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $image = $request->file('image');
        $uuid = (string) Str::uuid();
        $extension = $image->clientExtension();
        $imageName = implode('.', [$uuid, $extension]);
        $request->file('image')->storeAs('public/images', $imageName);

        DB::insert('INSERT INTO images (uuid, extension, status) VALUES (?, ?, ?)', [$uuid, $extension, 'waiting']);

        ImageProcess::dispatch($imageName);

        return response()->json(['uuid' => $uuid,], 201);
    }

    public function getById($uuid): \Illuminate\Http\JsonResponse
    {
        $image = DB::select('SELECT * FROM images WHERE uuid = ?', [$uuid]);
        $image = (array) $image[0];

        if ($image['status'] == null) return response()->json(['response' => 'not found',], 404);
        else if ($image['status'] == 'finished') return response()->json(
            ['files' => [
                storage_path() . '/app/public/resized/' . $image['uuid'] . '-100.' . $image['extension'],
                storage_path() . '/app/public/resized/' . $image['uuid'] . '-200.' . $image['extension'],
                storage_path() . '/app/public/resized/' . $image['uuid'] . '-300.' . $image['extension'],
                storage_path() . '/app/public/resized/' . $image['uuid'] . '-400.' . $image['extension'],
                ],
            ], 201);
        else if ($image['status'] == 'pending') return response()->json(['status' => 'pending',], 201);
        else return response()->json(['status' => 'waiting',], 201);
    }

    public function getByPath($path)
    {
        if (file_exists($path)) {
            $parts = explode('/', $path);
            $image = storage_path() . '/app/public/resized/' . end($parts);
            $headers = array( 'Content-Type: image/' . explode('.', end($parts))[1], );
            return response()->download($image, end($parts), $headers);
        } else {
            return response()->json(['response' => 'not found',], 404);
        }
    }

    public function getRandomExistingId(): array
    {
        return DB::select('SELECT * FROM images ORDER BY rand() LIMIT 1');
    }

    public function getRandomFile($directory)
    {
        $files = Storage::allFiles($directory);
        return $files[rand(0, count($files) - 1)];
    }
}
