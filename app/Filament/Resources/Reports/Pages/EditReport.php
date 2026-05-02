<?php

namespace App\Filament\Resources\Reports\Pages;

use App\Filament\Resources\Reports\ReportResource;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] !== 'open') {
            $data['handled_by'] = auth()->id();
            $data['handled_at'] = now();
        } else {
            $data['handled_by'] = null;
            $data['handled_at'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return ReportResource::getUrl('index');
    }
}
