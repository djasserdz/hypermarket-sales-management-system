<?php


// app/Filament/Resources/SupermarketResource/Pages/CreateSupermarket.php
namespace App\Filament\Resources\SupermarketResource\Pages;

use App\Filament\Resources\SupermarketResource;
use App\Models\location;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateSupermarket extends CreateRecord
{
    protected static string $resource = SupermarketResource::class;

    protected function beforeCreate(): void
    {

        $street = $this->data['location']['street_name'];
        $state = $this->data['location']['state'];


        $coordinates = $this->geocodeAddress($street, $state);


        $this->data['location']['latitude'] = $coordinates['lat'];
        $this->data['location']['longitude'] = $coordinates['lng'];
    }

    protected function afterCreate(): void
    {

    location::create([
            'supermarket_id' => $this->record->id,
            'street_name' => $this->data['location']['street_name'],
            'state' => $this->data['location']['state'],
            'latitude' => $this->data['location']['latitude'],
            'longitude' => $this->data['location']['longitude'],
        ]);
    }


    protected function geocodeAddress(string $street, string $state): array
    {
        $address = urlencode($street . ', ' . $state);

        $response = Http::withHeaders([
            'User-Agent' => 'YourApp/1.0',
        ])->get("https://nominatim.openstreetmap.org/search?format=json&q={$address}");

        if ($response->successful() && count($response->json()) > 0) {
            $location = $response->json()[0];
            return [
                'lat' => $location['lat'],
                'lng' => $location['lon'],
            ];
        }


        return [
            'lat' => '0',
            'lng' => '0',
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('find_coordinates')
                ->label('Find Coordinates')
                ->form([
                    TextInput::make('street_name')
                        ->label('Street Name')
                        ->required(),
                    TextInput::make('state')
                        ->label('State')
                        ->required(),
                ])
                ->action(function (array $data, Set $set) {

                    $street = $data['street_name'];
                    $state = $data['state'];

                    if ($street && $state) {
                        $coordinates = $this->geocodeAddress($street, $state);


                        $this->data['location']['street_name'] = $street;
                        $this->data['location']['state'] = $state;
                        $this->data['location']['latitude'] = $coordinates['lat'];
                        $this->data['location']['longitude'] = $coordinates['lng'];


                        $this->fillForm();
                        Notification::make()
                            ->title('Coordinates found')
                            ->body("Latitude: {$coordinates['lat']}, Longitude: {$coordinates['lng']}")
                            ->success()
                            ->send();
                    }
                })
        ];
    }
}
