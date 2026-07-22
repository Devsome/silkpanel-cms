<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Concerns\InteractsWithLockedState;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsComponent;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

/**
 * Base page for a single section of the site settings.
 *
 * Each concrete page contributes only its own form schema and the setting
 * keys it owns; filling, persisting and the shared save action live here so
 * every settings page behaves identically.
 *
 * @property-read Schema $form
 */
abstract class AbstractSettingsPage extends Page
{
    use InteractsWithLockedState;

    protected static ?string $cluster = Settings::class;

    protected string $view = 'filament.clusters.settings.page';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    /**
     * The form schema for this settings section.
     *
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    abstract protected function getFormSchema(): array;

    /**
     * The setting keys this page is responsible for persisting.
     *
     * @return array<int, string>
     */
    abstract protected function getSettingKeys(): array;

    /**
     * Number of grid columns for the page's form container.
     *
     * @return int|array<string, int|null>
     */
    protected function getFormColumns(): int|array
    {
        return 2;
    }

    public function mount(): void
    {
        $this->form->fill($this->getSettingsArray());
    }

    public function form(Schema $schema): Schema
    {
        $locked = $this->isLocked();

        return $schema
            ->components([
                Form::make($this->getFormSchema())
                    ->columns($this->getFormColumns())
                    ->disabled($locked)
                    ->livewireSubmitHandler($locked ? null : 'save')
                    ->footer($locked ? [] : [
                        ActionsComponent::make([
                            Action::make('save')
                                ->label(__('filament/settings.actions.save'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if ($this->isLocked()) {
            return;
        }

        $data = $this->form->getState();

        if (! $this->beforeSave($data)) {
            return;
        }

        foreach ($this->getSettingKeys() as $key) {
            if (isset($data[$key]) && $data[$key] !== null) {
                Setting::set($key, $data[$key], null, null, null);
            }
        }

        $this->afterSave($data);

        Notification::make()
            ->success()
            ->title(__('filament/settings.notifications.updated_title'))
            ->body(__('filament/settings.notifications.updated_message'))
            ->send();
    }

    /**
     * Hook to validate state before persisting. Return false to abort the
     * save (the page is responsible for notifying the user in that case).
     *
     * @param  array<string, mixed>  $data
     */
    protected function beforeSave(array $data): bool
    {
        return true;
    }

    /**
     * Hook to persist anything that does not fit the standard key loop.
     *
     * @param  array<string, mixed>  $data
     */
    protected function afterSave(array $data): void
    {
    }

    /**
     * All settings as an associative array, used to fill the form.
     *
     * @return array<string, mixed>
     */
    private function getSettingsArray(): array
    {
        return Setting::getAllSettings();
    }
}
