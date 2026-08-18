<?php

namespace Plugins\CustomBlocks\Blocks\Contact;

use App\Blocks\Block;
use App\Blocks\Field;

class Contact extends Block
{
    public string $name = 'contact';

    public string $label = 'Contact';

    public function fields(): array
    {
        return [
            Field::string('heading', 'Heading', default: 'Contact Us'),
            Field::text('subheading', 'Subheading', default: 'Have a question or need assistance? Reach out to us and our team will get back to you promptly.'),
            Field::map('mapEmbedUrl', 'Map Embed URL', default: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153167!3d-37.81627997975159!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d5df1f5a2f7%3A0x5045675218ce6e0!2sMelbourne%20VIC%2C%20Australia!5e0!3m2!1sen!2s!4v1694259649753!5m2!1sen!2s'),
            Field::group('email', 'Email', [
                Field::string('title', 'Title', default: 'Email'),
                Field::text('description', 'Description', default: 'Send us an email anytime'),
                Field::string('value', 'Email Address', default: 'info@example.com'),
            ]),
            Field::group('contactPhone', 'Phone', [
                Field::string('title', 'Title', default: 'Phone'),
                Field::text('description', 'Description', default: 'Give us a call'),
                Field::string('value', 'Phone Number', default: '+1 (555) 123-4567'),
            ]),
            Field::group('office', 'Office', [
                Field::string('title', 'Title', default: 'Office'),
                Field::text('description', 'Description', default: 'Visit our office'),
                Field::string('value', 'Address', default: 'Zindabazar, Sylhet, Bangladesh'),
            ]),
        ];
    }
}
