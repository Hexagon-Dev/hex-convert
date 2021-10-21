<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageName;
    protected $uuid;
    protected $extension;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($imageName)
    {
        $this->imageName = $imageName;
        $parts = explode('.', $imageName);
        $this->uuid = $parts[0];
        $this->extension = $parts[1];
        Log::debug('imageName: ' . $imageName);

        DB::update('UPDATE images SET status = "pending" WHERE uuid = ?', [$this->uuid]);
        Log::debug('[DB] uuid: ' . $this->uuid . ' status: pending');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $size = 100;
        for ($i = 0; $i < 4; $i++)
        {
            $resizedImageName = $this->uuid . '-' . $size . '.' . $this->extension;
            $resizedImagePath = storage_path() . '/app/public/resized/' . $resizedImageName;
            $resizedImage = Image::make(storage_path() . '/app/public/images/' . $this->imageName)->resize($size, $size);
            $resizedImage->save($resizedImagePath);
            Log::debug('Created ' . $size . ' image: ' . $resizedImagePath);
            $size += 100;
        }

        DB::update('UPDATE images SET status = "finished" WHERE uuid = ?', [$this->uuid]);
        Log::debug('[DB] uuid: ' . $this->uuid . ' status: finished');
    }
}
