<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ImageServiceInterface;
use Illuminate\Database\Eloquent\Collection;
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

        $collection = $this->service->upload($request->file('image'));
        return response()->json(
            ['id' => $collection->get('id')],
            $collection->get('status')
        );
    }

    /**
     * @param string $uuid
     * @return JsonResponse
     */
    public function info(string $uuid): JsonResponse
    {
        $collection = $this->service->info($uuid);
        return response()->json(
            ['files' => $collection->get('files')],
            $collection->get('status')
        );
    }

    /**
     * @param string $path
     * @return \Illuminate\Support\Collection|JsonResponse
     */
    public function download(string $path)
    {
        $collection = $this->service->download($path);
        if ($collection->get('status') == 404) {
            return response()->json(
                ['error' => $collection->get('error')],
                $collection->get('status')
            );
        }
        return $collection;
    }
}
