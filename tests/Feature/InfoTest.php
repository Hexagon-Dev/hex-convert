<?php

namespace Tests\Feature;

use App\Models\Image;
use Illuminate\Support\Str;
use Tests\TestCase;

class InfoTest extends TestCase
{
    /**
     * @test
     */
    public function infoNotExists(): void
    {
        $response = $this->get(route('info', ['uuid' => Str::uuid()->toString()]));
        $response->assertStatus(404);
        self::assertSame('task not exists', $response->json('error'));
    }

    /**
     * @test
     */
    public function infoWaiting(): void
    {
        /** @var Image $image */
        $image = Image::query()->create([
           'uuid' => Str::uuid()->toString(),
           'path' => Str::random(),
           'status' => Image::STATUS_WAITING,
        ]);

        $response = $this->get(route('info', ['uuid' => $image->uuid]));
        $response->assertStatus(202);
        self::assertSame('the task is not yet done', $response->json('message'));
    }

    /**
     * @test
     */
    public function infoProcessing(): void
    {
        /** @var Image $image */
        $image = Image::query()->create([
            'uuid' => Str::uuid()->toString(),
            'path' => Str::random(),
            'status' => Image::STATUS_PROCESSING,
        ]);

        $response = $this->get(route('info', ['uuid' => $image->uuid]));
        $response->assertStatus(202);
        self::assertSame('the task is not yet done', $response->json('message'));
    }

    /**
     * @test
     */
    public function infoDone(): void
    {
        /** @var Image $image */
        $image = Image::query()->create([
            'uuid' => Str::uuid()->toString(),
            'path' => Str::random(),
            'status' => Image::STATUS_DONE,
        ]);

        $response = $this->get(route('info', ['uuid' => $image->uuid]));
        $response->assertStatus(200);
        self::assertSame([
            '/images/resized/100x100-' . $image->path,
            '/images/resized/200x200-' . $image->path,
            '/images/resized/300x300-' . $image->path,
            '/images/resized/500x500-' . $image->path,
        ], $response->json('files'));
    }
}
