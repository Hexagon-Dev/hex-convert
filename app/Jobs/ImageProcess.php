<?php

namespace App\Jobs;

use App\Models\Image as ImageModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;

class ImageProcess implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        /** @var ImageModel $image */
        $image = ImageModel::query()->find($this->uuid);

        if (!$image) {
            return;
        }

        $image->update([
            'status' => ImageModel::STATUS_PROCESSING,
        ]);

        collect([100, 200, 300, 500])->each(function ($size) use ($image) {
            $path = $image->path;
            $resizedPath = public_path("images/resized/${size}x${size}-$path");
            Image::make(public_path('images/' . $image->path))->resize($size, $size)->save($resizedPath);
        });

        $image->update([
            'status' => ImageModel::STATUS_DONE,
        ]);
    }
}
