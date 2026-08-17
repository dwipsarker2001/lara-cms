<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;
use App\Models\Form;
use Illuminate\Support\Facades\Schema;

class CheckoutForm extends Block
{
    public string $name = 'checkoutForm';

    public string $label = 'Travel Booking & Order Summary';

    public bool $background = true;

    public function fields(): array
    {
        return [
            Field::select('formId', 'Select Form', self::formOptions(), default: ''),
            Field::string('formTitle', 'Booking Details Title', default: 'Traveler Details'),
            Field::string('summaryTitle', 'Order Summary Title', default: 'Order Summary'),
            Field::string('productName', 'Package Name', default: 'Gourmet Coffee Beans'),
            Field::text('productDesc', 'Package Description', default: 'Premium quality, ethically sourced.'),
            Field::image('productImage', 'Package Image', default: 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?q=80&w=300&auto=format&fit=crop'),
            Field::number('adultPrice', 'Adult Price ($)', default: 99.00),
            Field::number('childPrice', 'Child Price ($)', default: 49.50),
            Field::number('extraService', 'Extra Service ($)', default: 0.00),
            Field::string('buttonText', 'Button Text', default: 'Confirm Booking'),
            Field::form('mapFullName', 'Full Name Form Key', formFieldKey: 'formId', default: 'full_name'),
            Field::form('mapEmail', 'Email Form Key', formFieldKey: 'formId', default: 'email'),
            Field::form('mapPhone', 'Phone Form Key', formFieldKey: 'formId', default: 'phone'),
            Field::form('mapTravelDate', 'Travel Date Form Key', formFieldKey: 'formId', default: 'travel_date'),
            Field::form('mapPreferredTime', 'Preferred Time Form Key', formFieldKey: 'formId', default: 'preferred_time'),
            Field::form('mapAdults', 'Adults Form Key', formFieldKey: 'formId', default: 'adults'),
            Field::form('mapChildren', 'Children Form Key', formFieldKey: 'formId', default: 'children'),
            Field::form('mapMessage', 'Message Form Key', formFieldKey: 'formId', default: 'additional_message'),
        ];
    }

    /** Helper to build form select options */
    protected static function formOptions(): array
    {
        $options = [
            ['value' => '', 'label' => 'Default Form'],
        ];

        try {
            if (Schema::hasTable('forms')) {
                $forms = Form::select('id', 'title')->orderBy('position')->get();
                foreach ($forms as $f) {
                    $options[] = [
                        'value' => (string) $f->id,
                        'label' => $f->title,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $options;
    }
}
