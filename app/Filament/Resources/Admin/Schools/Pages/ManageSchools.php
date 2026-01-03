<?php

namespace App\Filament\Resources\Admin\Schools\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Admin\Schools\SchoolResource;

class ManageSchools extends ManageRecords
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Yeni Okul Ekle')
                ->button(),
        ];
    }
}
