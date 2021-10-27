<?php

namespace App\Services;

use App\Contracts\Services\ImageServiceInterface;
use App\Jobs\ImageProcess;
use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageService implements ImageServiceInterface
{
    /**
     * @param UploadedFile $file
     * @return Collection
     */
    public function upload(UploadedFile $file): Collection
    {
        $uuid = (string)Str::uuid();

        /**
         * Saving file to the public folder
         */
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

        return Collection::make([
            'id' => $image->uuid,
            'status' => Response::HTTP_CREATED
            ]);
    }

    /**
     * @param string $uuid
     * @return Collection
     */
    public function info(string $uuid): Collection
    {

        /** @var Image $image */
        $image = Image::query()->find($uuid);

        if ($image === null) {
            return Collection::make([
                'error' => 'task does not exist',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }

        if ($image->status !== Image::STATUS_DONE) {
            return Collection::make([
                'message' => 'task is not yet done',
                'status' => Response::HTTP_ACCEPTED
            ]);
        }

        $files = collect([100, 200, 300, 500])->map(function ($size) use ($image) {
            $path = $image->path;
            return "/images/resized/${size}x${size}-$path";
        });

        return Collection::make([
            'files' => $files,
            'status' => Response::HTTP_OK
            ]);
    }

    /**
     * @param string $path
     * @return Collection|BinaryFileResponse
     */
    public function download(string $path): Collection
    {
        if (!File::exists(public_path($path))) {
            return Collection::make([
                'error' => 'file not found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }

        return response()->file(public_path($path));
    }
}
