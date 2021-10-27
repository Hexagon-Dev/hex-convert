<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    /**
     * @param ImageService $ImageService
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(ImageService $ImageService, Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        return $ImageService->upload($request);
    }

    /**
     * @param ImageService $ImageService
     * @param string $uuid
     * @return JsonResponse
     */
    public function info(ImageService $ImageService, string $uuid): JsonResponse
    {
        return $ImageService->info($uuid);
    }

    /**
     * @param ImageService $ImageService
     * @param string $path
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(ImageService $ImageService, string $path)
    {
        return $ImageService->download($path);
    }
}
