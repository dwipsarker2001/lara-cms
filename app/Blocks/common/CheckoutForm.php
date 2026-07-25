<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class CheckoutForm extends Block
{
    public string $name = 'checkoutForm';

    public string $label = 'Travel Booking & Order Summary';

    public bool $background = true;

    public function fields(): array
    {
        return [
            Field::string('formTitle', 'Booking Details Title', default: 'Traveler Details'),
            Field::string('summaryTitle', 'Order Summary Title', default: 'Order Summary'),
            Field::string('productName', 'Package Name', default: 'Gourmet Coffee Beans'),
            Field::text('productDesc', 'Package Description', default: 'Premium quality, ethically sourced.'),
            Field::image('productImage', 'Package Image', default: 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?q=80&w=300&auto=format&fit=crop'),
            Field::string('subtotal', 'Subtotal Amount', default: '$99.00'),
            Field::string('shipping', 'Service Fee', default: '$5.00'),
            Field::string('tax', 'Tax Amount', default: '$8.92'),
            Field::string('total', 'Total Amount', default: '$112.92'),
            Field::string('buttonText', 'Button Text', default: 'Confirm Booking'),
        ];
    }
}
