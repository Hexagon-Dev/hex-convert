<?php

namespace App\Contracts\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

interface ImageServiceInterface
{
    /**
     * @param UploadedFile $file
     * @return JsonResponse
     */
    public function upload(UploadedFile $file): JsonResponse;
}
