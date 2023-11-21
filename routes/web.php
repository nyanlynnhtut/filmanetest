<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
use Spatie\Image\Image;

Route::get('watermark', function() {
    $contents = \Storage::get('https://npspace.sgp1.cdn.digitaloceanspaces.com/test-watermark.png');

    dd($contents);

    $image = Image::load();



    $image->watermark(public_path('statics/watermark-text.png'))
      ->watermarkOpacity(50)
      ->save(public_path('water.png'));

    dd($image);

});

use Intervention\Image\ImageManager;
Route::get('wm', function() {
    $manager = new ImageManager(['driver' => 'imagick']);
    $image = $manager->make('https://npspace.sgp1.cdn.digitaloceanspaces.com/test-watermark.png');
    $image->insert(public_path('statics/watermark-text.png'), 'bottom-right', 10, 10);
    $image->save(public_path('statics/new-image.png'));
    dd($image);
    
});
