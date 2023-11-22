<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Image;
use App\Models\Patient;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected $fImage;
    protected $bImage;
    protected $sImage = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->fImage = $data['front_image'];
        $this->bImage = $data['back_image'];
        $this->sImage = $data['selfie_image'];

        unset($data['front_image'], $data['back_image'], $data['selfie_image']);
     
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveImage($this->fImage, 'front');
        $this->saveImage($this->bImage, 'back');
        foreach ($this->sImage as $img) {
            $this->saveImage($img, 'selfie');
        }
    }

    protected function saveImage($path, $type)
    {
        Image::create([
            'url' => $path,
            'type' => $type,
            'imageable_type' => $this->getModel(), 
            'imageable_id' => $this->record->id
        ]);
    }
}
