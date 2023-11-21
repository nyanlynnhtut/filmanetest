<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Image;
use App\Models\Patient;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $fImage = $data['front_image'];
        $bImage = $data['back_image'];
        $sImage = $data['selfie_image'];

        $this->saveImage($fImage, 'front');
        $this->saveImage($bImage, 'back');
        foreach ($sImage as $img) {
            $this->saveImage($img, 'selfie');
        }

        unset($data['front_image'], $data['back_image'], $data['selfie_image']);
     
        return $data;
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
