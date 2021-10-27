<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    /**
     * @var ImageService
     */
    protected ImageService $service;

    /**
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->service = $imageService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        return $this->service->upload($request);
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function info(string $uuid): JsonResponse
    {
        return $this->service->info($uuid);
    }

    /**
     * @param string $path
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(string $path)
    {
        return $this->service->download($path);
    }
}
