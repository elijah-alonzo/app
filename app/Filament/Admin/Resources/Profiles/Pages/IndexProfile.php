<?php

namespace App\Filament\Admin\Resources\Profiles\Pages;

use App\Filament\Admin\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\Page;

class IndexProfile extends Page
{
    protected static string $resource = ProfileResource::class;

    public function mount(): void
    {
        // Redirect directly to the current user's profile edit page
        $this->redirect(ProfileResource::getUrl('edit', ['record' => auth()->id()]));
    }
}
