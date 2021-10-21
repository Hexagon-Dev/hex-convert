<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UploadTest extends TestCase
{
    /**
     * @test
     */
    public function upload(): void
    {
        $response = $this->post(route('upload'), [
            'image' => UploadedFile::fake()->image('test.jpg', 1, 1),
        ]);

        $response->assertStatus(201);

        /** @var Image $image */
        $image = Image::query()->first();

        self::assertTrue(File::exists(public_path('images/' . $image->path)));
    }
}
