<?php

namespace Tests\Feature;

use App\Http\Controllers\ImageController;
use App\Jobs\ImageProcess;
use http\Env\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class ImageTest extends TestCase
{
    private $header = ["User-Agent" => "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0"];
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testImageLoad()
    {
        Storage::fake('images');

        $image = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->withHeaders([$this->header])->json('POST', '/api/tasks', ['image' => $image]);

        $response->assertStatus(201);
    }

    public function testGetById()
    {
        $entry = ImageController::getRandomExistingId();
        $entry = (array) $entry[0];

        $response = $this->withHeaders([$this->header])->json('GET', '/api/tasks/' . $entry['uuid']);

        if ($response->getStatusCode() == 201) $response->assertStatus(201);
        else $response->assertStatus(200);
    }

    public function testGetByPath()
    {
        ImageController::getRandomFile(storage_path() . '/app/public/resized/');
    }
}
