<?php

namespace App\Services;

use App\Jobs\ImageProcess;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageService
{
    public function upload(Request $request): JsonResponse
    {
        $uuid = (string)Str::uuid();

        /**
         * Saving file to the public folder
         */
        $file = $request->file('image');
        $extension = $file->clientExtension();
        $imageName = implode('.', [$uuid, $extension]);

        File::put(public_path('images/' . $imageName), $file->getContent());

        /**
         * Saving to the database
         */
        /** @var Image $image */
        $image = Image::query()->create([
            'uuid' => $uuid,
            'path' => $imageName,
            'status' => Image::STATUS_WAITING,
        ]);

        /**
         * Exactly dispatching to the queue
         */
        ImageProcess::dispatch($image->uuid);

        return response()->json(
            ['id' => $image->uuid],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function info(string $uuid): JsonResponse
    {

        /** @var Image $image */
        $image = Image::query()->find($uuid);

        if ($image === null) {
            return response()->json(
                ['error' => 'task does not exist'],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($image->status !== Image::STATUS_DONE) {
            return response()->json(
                ['message' => 'task is not yet done'],
                Response::HTTP_ACCEPTED
            );
        }

        $files = collect([100, 200, 300, 500])->map(function ($size) use ($image) {
            $path = $image->path;
            return "/images/resized/${size}x${size}-$path";
        });

        return response()->json(
            ['files' => $files],
            Response::HTTP_OK
        );
    }

    /**
     * @param string $path
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(string $path)
    {
        if (!File::exists(public_path($path))) {
            return response()->json(
                ['error' => 'file not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->file(public_path($path));
    }
}
