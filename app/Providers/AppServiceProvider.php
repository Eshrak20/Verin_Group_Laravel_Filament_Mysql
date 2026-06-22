<?php

namespace App\Providers;

use App\Models\ProductVariantImage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register your VariantImage model observer
        ProductVariantImage::observe(\App\Observers\VariantImageObserver::class);

        // Define our custom Cloudinary driver for Laravel
        Storage::extend('cloudinary', function ($app, $config) {

            $cloudinaryConfig = new Configuration([
                'cloud' => [
                    'cloud_name' => $config['cloud_name'],
                    'api_key'    => $config['api_key'],
                    'api_secret' => $config['api_secret'],
                ],
                'url' => ['secure' => true]
            ]);

            $uploadApi = new UploadApi($cloudinaryConfig);

            // Create a custom Flysystem V3 implementation inline
            $adapter = new class($uploadApi) implements \League\Flysystem\FilesystemAdapter {
                protected $api;

                public function __construct($api)
                {
                    $this->api = $api;
                }

                public function url(string $path): string
                {
                    $cloudName = config('filesystems.disks.cloudinary.cloud_name');
                    
                    $filename = pathinfo($path, PATHINFO_FILENAME);
                    $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'png';

                    return "https://res.cloudinary.com/{$cloudName}/image/upload/{$filename}.{$extension}";
                }

                public function write(string $path, string $contents, \League\Flysystem\Config $config): void
                {
                    $base64 = 'data:image/png;base64,' . base64_encode($contents);
                    $this->api->upload($base64, [
                        'public_id' => pathinfo($path, PATHINFO_FILENAME),
                        'overwrite' => true
                    ]);
                }

                public function writeStream(string $path, $contents, \League\Flysystem\Config $config): void
                {
                    $this->write($path, stream_get_contents($contents), $config);
                }

                public function fileExists(string $path): bool
                {
                    return !empty($path);
                }

                public function directoryExists(string $path): bool
                {
                    return false;
                }

                public function read(string $path): string
                {
                    return '';
                }

                public function readStream(string $path)
                {
                    return null;
                }

                public function delete(string $path): void
                {
                    $publicId = pathinfo($path, PATHINFO_FILENAME);
                    try {
                        $this->api->destroy($publicId);
                    } catch (\Exception $e) {
                        logger()->error("Cloudinary delete failed for ID {$publicId}: " . $e->getMessage());
                    }
                }

                public function deleteDirectory(string $path): void {}
                public function createDirectory(string $path, \League\Flysystem\Config $config): void {}
                public function setVisibility(string $path, string $visibility): void {}
                
                public function visibility(string $path): \League\Flysystem\FileAttributes
                {
                    return new \League\Flysystem\FileAttributes($path);
                }

                /**
                 * FIX: Return FileAttributes populated with a fallback dynamic mime-type.
                 */
                public function mimeType(string $path): \League\Flysystem\FileAttributes
                {
                    $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'png';
                    $mime = ($extension === 'jpg' || $extension === 'jpeg') ? 'image/jpeg' : 'image/png';

                    return new \League\Flysystem\FileAttributes(
                        $path,
                        null,
                        null,
                        null,
                        $mime
                    );
                }

                /**
                 * FIX: Return FileAttributes populated with a fallback integer timestamp.
                 */
                public function lastModified(string $path): \League\Flysystem\FileAttributes
                {
                    return new \League\Flysystem\FileAttributes(
                        $path,
                        null,
                        null,
                        time() // Fallback timestamp
                    );
                }

                /**
                 * FIX: Return FileAttributes populated with a fallback integer file size.
                 */
                public function fileSize(string $path): \League\Flysystem\FileAttributes
                {
                    return new \League\Flysystem\FileAttributes(
                        $path,
                        0 // Fallback size in bytes
                    );
                }

                public function listContents(string $path, bool $deep): iterable
                {
                    return [];
                }

                public function move(string $source, string $destination, \League\Flysystem\Config $config): void {}
                public function copy(string $source, string $destination, \League\Flysystem\Config $config): void {}
            };

            return new class(new Filesystem($adapter, $config), $adapter, $config) extends FilesystemAdapter {
                /**
                 * Overwrite native URL call directly so Laravel's driver natively supports it
                 */
                public function url($path): string
                {
                    return $this->getAdapter()->url($path);
                }
            };
        });
    }
}