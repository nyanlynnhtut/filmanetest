<?php

namespace Za\Support\ZaImage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ZaImage
{
    protected string $rootPath;

    protected string $path;

    protected ?string $imageUrl = '';

    protected $image;

    public function __construct()
    {
        $this->rootPath = config('za_image.root_path');
    }

    /**
     * disk
     */
    protected function disk(): Filesystem
    {
        return Storage::disk();
    }

    /**
     * To set image URL
     */
    public function setUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function resize(?string $path = 'product', array $desiredSizes = [])
    {
        if (! $this->imageUrl) {
            return;
        }

        $sizes = config('za_image.image_sizes');

        if (count($desiredSizes)) {
            $sizes = collect($sizes)->filter(function ($size, $key) use ($desiredSizes) {
                if (in_array($key, $desiredSizes)) {
                    return $size;
                }
            })->toArray();
        }

        foreach ($sizes as $key => $imageSize) {
            $pxyImage = $this->imgpxy($imageSize['width'], $imageSize['height']);

            $this->upload(
                url: $pxyImage,
                name: $this->generateResizeImageNameWithBasename(key: $key),
                path: $path
            );
        }

        return $this;
    }

    public function getResizeImage(string $type): string
    {
        if ($this->checkImage(type: $type)) {
            $this->imageUrl = config('app.cdn_url')
                ? config('app.cdn_url') . '/' . $this->generateResizeImageName(key: $type)
                : $this->generateResizeImageName(key: $type);
        }

        return $this->imageUrl;
    }

    public function upload(string | UploadedFile $url, ?string $path = 'product', ?string $name = '')
    {
        if (! $name) {
            $name = $url instanceof UploadedFile ? $url->getClientOriginalName() : Str::uuid();
        }

        $this->path = $this->rootPath . '/' . $path . '/' . $name;

        $this->disk()->put($this->path, file_get_contents($url), 'public');

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function checkImage(string $type, string $path = 'product'): bool
    {
        return $this->disk()->exists($this->rootPath . '/' . $path . '/' . $this->generateResizeImageNameWithBasename(key: $type));
    }

    /**
     * file store lote yin thone mae name example: example_image-small.jpg
     */
    protected function generateResizeImageNameWithBasename(string $key): string
    {
        return substr(basename($this->imageUrl), 0, -1 * (strpos(strrev(basename($this->imageUrl)), '.') + 1)) . "-{$key}.png";
    }

    /**
     * image url ko lo chin tae type nae pyn tae name
     */
    protected function generateResizeImageName(string $key, $path = 'product'): string
    {
        // return substr($this->imageUrl, 0, -1 * (strpos(strrev($this->imageUrl), '.') + 1)) . "-{$key}.png";
        return $this->rootPath . '/' . $path . '/' . $this->generateResizeImageNameWithBasename(key: $key);
    }

    protected function imgpxy($width, $height = null, $resize = 'auto', $gravity = 'ce')
    {
        $resize = 'fit';
        $gravity = 'no';
        $key = 'db42fdd5f3ecab62e220f8b96e238ec3505fc5431c1def6320f38987b65c43e2f09f59102427bfd074921f3df72908ef35443fb8042b951908db0e507f053554';
        $salt = 'f2d0b720948271886560be15c69547a40bf534b60852bd159dd2c6e692b1c5178cbb8ffaea208962abdcaf5bd4209d5cbe40c859ea77526aabd750f0845f3ba2';

        $height = is_null($height) ? $width : $height;

        $keyBin = pack('H*', $key);
        if (empty($keyBin)) {
            exit('Key expected to be hex-encoded string');
        }

        $saltBin = pack('H*', $salt);
        if (empty($saltBin)) {
            exit('Salt expected to be hex-encoded string');
        }

        $enlarge = 0;
        $extension = 'png';

        $encodedUrl = rtrim(strtr(base64_encode($this->imageUrl), '+/', '-_'), '=');

        // $path = "/{$resize}/{$width}/{$height}/{$gravity}/{$enlarge}/{$encodedUrl}.{$extension}";
        $path = "/rs:{$resize}:{$width}:{$height}:{$enlarge}/g:{$gravity}/{$encodedUrl}.{$extension}";

        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $saltBin . $path, $keyBin, true)), '+/', '-_'), '=');

        return sprintf('%s/%s%s', 'https://imgproxy.marketplace.com.mm', $signature, $path);
    }
}
