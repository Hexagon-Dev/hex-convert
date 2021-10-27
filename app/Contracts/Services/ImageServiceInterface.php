<?php

namespace App\Contracts\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface ImageServiceInterface
{
    /**
     * @param UploadedFile $file
     * @return Collection
     */
    public function upload(UploadedFile $file): Collection;

    /**
     * @param string $uuid
     * @return Collection
     */
    public function info(string $uuid): Collection;

    /**
     * @param string $path
     * @return Collection
     */
    public function download(string $path): Collection;
}
