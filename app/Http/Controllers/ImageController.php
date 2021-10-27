<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ImageServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    protected ImageServiceInterface $service;

    /**
     * @param ImageServiceInterface $imageService
     */
    public function __construct(ImageServiceInterface $imageService)
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

        return $this->service->upload($request->file('image'));
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
