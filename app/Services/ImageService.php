<?php

namespace App\Services;

use App\Contracts\Services\ImageServiceInterface;
use App\Jobs\ImageProcess;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

        return $this->makeResponse(['id' => $image->uuid], Response::HTTP_CREATED);
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
            return $this->makeResponse(['error' => 'task does not exist'], Response::HTTP_NOT_FOUND);
        }

        if ($image->status !== Image::STATUS_DONE) {
            return $this->makeResponse(['message' => 'task is not yet done'], Response::HTTP_ACCEPTED);
        }

        $files = collect([100, 200, 300, 500])->map(function ($size) use ($image) {
            $path = $image->path;
            return "/images/resized/${size}x${size}-$path";
        });

        return $this->makeResponse(['files' => $files], Response::HTTP_OK);
    }

    /**
     * @param string $path
     * @return Collection|string
     */
    public function download(string $path)
    {
        if (!File::exists(public_path($path))) {
            return $this->makeResponse(['error' => 'file not found'], Response::HTTP_NOT_FOUND);
        }

        return public_path($path);
    }

    /**
     * @param array $data
     * @param int $status
     * @return Collection
     */
    protected function makeResponse(array $data, int $status): Collection
    {
        return collect([
            'data' => $data,
            'status' => $status,
        ]);
    }
}
