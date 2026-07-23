<?php

namespace App\Blocks\common;

use App\Blocks\Block;
use App\Blocks\Field;

class TravelDetails extends Block
{
    public string $name = 'travelDetails';

    public string $label = 'Travel Details';

    public bool $background = false;

    public function fields(): array
    {
        return [
            // ── Hero / Gallery ──
            Field::string('breadcrumb', 'Breadcrumb Text', default: 'Sylhet Adventure & Hiking Tour'),
            Field::string('title', 'Tour Title', default: 'Sylhet Adventure & Hiking Tour'),
            Field::string('rating', 'Rating', default: '4.8'),
            Field::string('reviewCount', 'Review Count', default: '2 reviews'),
            Field::string('location', 'Location', default: 'Sylhet, Bangladesh'),
            Field::list('galleryImages', 'Gallery Images', [
                Field::image('image', 'Image'),
            ], count: 4),

            // ── About ──
            Field::string('aboutTitle', 'About Section Title', default: 'About Tour Package'),
            Field::text('aboutDescription', 'About Description', default: 'Hiking in Jaintiapur Hills, trekking to Hamham Waterfall, exploring Khadimnagar National Park, and experiencing traditional Khasi village life. This 3-night, 4-day adventure is built for those who love the outdoors.'),

            // ── Quick Info Grid ──
            Field::list('quickInfo', 'Quick Info Items', [
                Field::icon('icon', 'Icon', default: 'fa-solid fa-bed'),
                Field::string('label', 'Label', default: 'Accommodation'),
                Field::string('value', 'Value', default: 'Eco Resort & Camp'),
            ], count: 8),

            // ── Booking Sidebar ──
            Field::string('priceLabel', 'Price Label', default: 'Starting From'),
            Field::string('originalPrice', 'Original Price', default: '৳9500'),
            Field::string('price', 'Current Price', default: '৳8500'),
            Field::string('priceSuffix', 'Price Suffix', default: 'per person'),
            Field::list('priceFeatures', 'Price Bullet Points', [
                Field::string('text', 'Feature Text', default: 'Money Back Guarantee.'),
            ], count: 2),
            Field::string('bookNowLabel', 'Book Now Label', default: 'Book Now'),
            Field::link('bookNowLink', 'Book Now Link', default: '#'),
            Field::string('whatsappLabel', 'WhatsApp Label', default: 'Chat on WhatsApp'),
            Field::link('whatsappLink', 'WhatsApp Link', default: '#'),
            Field::string('bookingNote', 'Booking Note', default: '🏕 Camping Under the Stars Included!'),

            // ── Explore Locations ──
            Field::string('locationsTitle', 'Locations Title', default: 'Explore Locations'),
            Field::list('locations', 'Locations', [
                Field::image('image', 'Image'),
                Field::string('name', 'Location Name', default: 'Jaintiapur Hills'),
                Field::string('duration', 'Duration', default: '11 days'),
            ], count: 3),

            // ── Highlights ──
            Field::string('highlightsTitle', 'Highlights Title', default: 'Highlights of the Tour'),
            Field::list('highlights', 'Highlights', [
                Field::string('text', 'Highlight Text', default: 'Trek to the summit of Chandranath Hill for sunrise views.'),
            ], count: 5),

            // ── Tour Itinerary ──
            Field::string('itineraryTitle', 'Itinerary Title', default: 'Tour Itinerary'),
            Field::list('itinerary', 'Itinerary Days', [
                Field::string('stopName', 'Stop Name (leave empty to continue previous stop)', default: ''),
                Field::string('departure', 'Departure Info', default: ''),
                Field::string('dayLabel', 'Day Label', default: 'Day-01'),
                Field::string('dayTitle', 'Day Title', default: 'Sylhet to Jaintiapur'),
                Field::text('dayDescription', 'Day Description', default: ''),
            ], count: 4),

            // ── Destination Map ──
            Field::string('mapTitle', 'Map Title', default: 'Package Destination Map'),
            Field::image('mapImage', 'Map Image'),

            // ── Features (Include / Exclude) ──
            Field::string('featuresTitle', 'Features Title', default: 'Package Features List'),
            Field::string('includeTitle', 'Include Column Title', default: 'Include Features'),
            Field::list('includeFeatures', 'Include Features', [
                Field::string('text', 'Feature', default: 'Eco Resort & Camping Accommodation'),
            ], count: 5),
            Field::string('excludeTitle', 'Exclude Column Title', default: 'Exclude Features'),
            Field::list('excludeFeatures', 'Exclude Features', [
                Field::string('text', 'Feature', default: 'Personal Trekking Gear & Shoes'),
            ], count: 4),

            // ── Additional Info ──
            Field::string('infoTitle', 'Additional Info Title', default: 'Additional Info'),
            Field::list('additionalInfo', 'Info Items', [
                Field::string('title', 'Title', default: 'Free Cancellation'),
                Field::text('description', 'Description', default: 'Free cancellation up to 7 days before departure. Cancellations within 7 days are subject to a 50% fee.'),
            ], count: 2),

            // ── FAQ ──
            Field::string('faqTitle', 'FAQ Title', default: 'Frequently Asked & Question'),
            Field::list('faqs', 'FAQ Items', [
                Field::string('question', 'Question', default: 'How difficult is the Chandranath Hill trek?'),
                Field::text('answer', 'Answer', default: 'The trek is of moderate difficulty, suitable for most fitness levels with basic hiking experience.'),
            ], count: 4),
        ];
    }
}
