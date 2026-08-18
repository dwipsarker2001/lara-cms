<?php

namespace Plugins\CustomBlocks\Blocks\BlogDetails;

use App\Blocks\Block;
use App\Blocks\Field;
use App\Models\Collection;
use Illuminate\Support\Facades\Schema;

class BlogDetails extends Block
{
    public string $name = 'blogDetails';

    public string $label = 'Blog Details';

    public bool $background = false;

    public function fields(): array
    {
        return [
            // ── Hero & Content ──
            Field::image('image', 'Featured Image', default: '', source: 'featured_image'),
            Field::string('title', 'Post Title', default: 'নেপাল ভ্রমণ: সাগরমাথার দেশে অ্যাডভেঞ্চার ও শান্তির সন্ধানে', source: 'title'),
            Field::tags('tag', 'Tags', source: 'tag'),
            Field::string('category', 'Category', default: 'Travel', source: 'category'),

            // ── Rich Text Main Content ──
            Field::richText('content', 'Blog Content', default: '<p>নেপাল—হিমালয়ের দেশ। পৃথিবীর সর্বোচ্চ পর্বতশৃঙ্গ মাউন্ট এভারেস্টের দেশ। বৌদ্ধ ও হিন্দু ধর্মের পবিত্র স্থান। ট্র্যাকার, মাউন্টেনিয়ার ও আধ্যাত্মিক সন্ধানীদের জন্য এটি এক অনন্য গন্তব্য।</p><h2>কাঠমান্ডু: ইতিহাসের শহর</h2><h3>দরবার স্কয়ার</h3><p>কাঠমান্ডু, পাটান ও ভক্তপুর—তিনটি শহরের নিজস্ব দরবার স্কয়ার আছে। ইউনেস্কো ওয়ার্ল্ড হেরিটেজ সাইট। রাজপ্রাসাদ, মন্দির ও প্রাচীন স্থাপত্যের অপূর্ব সংগ্রহ।</p><h3>স্বয়ম্ভূনাথ স্তূপ (মাঙ্কি টেম্পল)</h3><p>পাহাড়ের চূড়ায় অবস্থিত এই সোনালি স্তূপ। চারপাশ থেকে পুরো কাঠমান্ডু উপত্যকা দেখা যায়। বুদ্ধের চোখ আঁকা চূড়ার চারপাশে বানরের দল থাকে—এজন্যই নাম \'মাঙ্কি টেম্পল\'।</p><h2>পোখরা: শান্তির শহর</h2><p>কাঠমান্ডু থেকে বাস বা ৬-৭ ঘণ্টার পথ পোখরা। ফেওয়া লেকের পাড়ে গড়ে ওঠা এই শহরটি ট্র্যাকিং-এর বেস ক্যাম্প।</p><h2>কী করবেন</h2><ul><li><strong>ফেওয়া লেকে বোট:</strong> পাহাড়ের প্রতিবিম্ব পড়ে থাকা জলে নৌকা ভ্রমণ।</li><li><strong>সারানগোট সূর্যোদয়:</strong> হিমালয়ের চূড়ায় সূর্যোদয়ের দৃশ্য অপূর্ব।</li></ul>', source: 'content'),

            // ── Sidebar Settings ──
            Field::select('postCollection', 'Sidebar Posts Collection', self::collectionOptions(), default: '', source: 'recent_post'),

            // ── Related Posts ──
            Field::boolean('showRelatedPosts', 'Show Related Posts Section', default: true),
            Field::string('relatedTitle', 'Related Section Title', default: 'Related Posts'),
        ];
    }

    /** Helper to build collection select options */
    protected static function collectionOptions(): array
    {
        $options = [
            ['value' => '', 'label' => 'All Post Collections (Auto)'],
        ];

        try {
            if (Schema::hasTable('collections')) {
                $cols = Collection::select('slug', 'name')->get();
                foreach ($cols as $col) {
                    if ($col->slug !== 'pages') {
                        $options[] = [
                            'value' => $col->slug,
                            'label' => $col->name,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $options;
    }
}
