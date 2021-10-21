<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    /**
     * @test
     */
    public function pathNotExists(): void
    {
        $response = $this->get(route('download', ['path' => 'path/not/exists']));
        $response->assertStatus(404);
        self::assertSame('file not found', $response->json('error'));
    }

    /**
     * @test
     */
    public function download(): void
    {
        $this->post(route('upload'), [
            'image' => UploadedFile::fake()->image('test.jpg', 1, 1),
        ]);

        /** @var Image $image */
        $image = Image::query()->first();

        $response = $this->get(route('download', ['path' => 'images/' . $image->path]));
        $response->assertStatus(200);
    }
}
