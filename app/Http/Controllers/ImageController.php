<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ImageServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $response = $this->service->upload($request->file('image'));

        return $this->responseJSON($response);
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function info(string $uuid): JsonResponse
    {
        $response = $this->service->info($uuid);

        return $this->responseJSON($response);
    }

    /**
     * @param string $path
     * @return BinaryFileResponse|JsonResponse
     */
    public function download(string $path)
    {
        $file = $this->service->download($path);

        if ($file instanceof Collection) {
            return $this->responseJSON($file);
        }

        return response()->file($file);
    }

    /**
     * @param Collection $response
     * @return JsonResponse
     */
    protected function responseJSON(Collection $response): JsonResponse
    {
        return response()->json(
            $response->get('data'),
            $response->get('status')
        );
    }
}
