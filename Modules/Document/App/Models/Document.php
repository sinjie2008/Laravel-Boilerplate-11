<?php

namespace Modules\Document\App\Models; // Updated namespace

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Document extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'description'];

    // Optional: Define factory relationship if needed within the module
    // protected static function newFactory()
    // {
    //     return \Modules\Document\Database\factories\DocumentFactory::new();
    // }

    public function getFileUrl()
    {
        // Check if the storage link needs adjustment based on module setup
        if ($this->hasMedia('documents')) {
            $media = $this->getFirstMedia('documents');
            // This URL generation might need review depending on how public storage is linked
            return url('storage/media/' . $media->id . '/' . $media->file_name);
        }
        return null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->useDisk('media') // Ensure 'media' disk is configured correctly in config/filesystems.php
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Ensure image processing libraries (like GD or Imagick) are available
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100);
    }
}
