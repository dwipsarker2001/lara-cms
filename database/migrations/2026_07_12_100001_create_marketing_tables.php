<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('from_email')->nullable();
            $table->string('from_name')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('name_to')->nullable();
            $table->json('receiver_emails')->nullable();
            $table->string('subject_line', 1000)->nullable();
            $table->string('preview_text', 1000)->nullable();
            $table->string('template_id')->nullable();
            $table->boolean('active_google_analytics')->default(false);
            $table->boolean('embed_images')->default(false);
            $table->boolean('add_tag')->default(false);
            $table->boolean('add_attachment')->default(false);
            $table->boolean('custom_unsubscribe')->default(false);
            $table->boolean('update_profile_form')->default(false);
            $table->boolean('enable_mirror')->default(false);
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('sms')->nullable();
            $table->string('whatsapp')->nullable();
            $table->boolean('double_opt_in')->default(false);
            $table->boolean('opt_in')->default(false);
            $table->boolean('is_unsubscribed')->default(false)->index();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('template_id');
            $table->timestamps();
        });

        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('camp_id')->constrained('campaigns')->cascadeOnDelete();
            $table->text('opened')->nullable();
            $table->text('clicked')->nullable();
            $table->text('bounced')->nullable();
            $table->text('black_list')->nullable();
            $table->text('total_sent')->nullable();
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camp_id')->constrained('campaigns')->cascadeOnDelete();
            $table->date('schedule_date')->nullable();
            $table->time('schedule_time')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();
        });

        Schema::create('senders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('domain');
            $table->string('sendgrid_id');
            $table->timestamps();
        });

        Schema::create('default_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('timezone')->nullable();
            $table->string('delay_time')->nullable();
            $table->string('time_format')->nullable();
            $table->string('date_format')->nullable();
            $table->boolean('image_url_hide')->default(false);
            $table->boolean('disable_notification')->default(false);
            $table->string('default_from_name')->nullable();
            $table->string('default_from_email')->nullable();
            $table->text('default_header')->nullable();
            $table->text('default_footer')->nullable();
            $table->string('default_reply_to')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('account_type')->nullable();
            $table->string('company_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('employees_number')->nullable();
            $table->boolean('is_email')->default(false);
            $table->boolean('is_phone')->default(false);
            $table->boolean('is_sms')->default(false);
            $table->boolean('is_post')->default(false);
            $table->timestamps();
        });

        Schema::create('report_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('report_enabled')->default(false);
            $table->timestamp('last_report_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_preferences');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('marketing_forms');
        Schema::dropIfExists('default_settings');
        Schema::dropIfExists('senders');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('stats');
        Schema::dropIfExists('templates');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('campaigns');
    }
};
