<?php

namespace App\Jobs;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SMTPValidateEmail\Validator;

class ProcessEmailFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $path = storage_path('app/public/uploads/'.$this->fileName);
        if (! file_exists($path)) {
            return;
        }

        $processedDir = storage_path('app/public/uploads/processed/');
        if (! is_dir($processedDir)) {
            mkdir($processedDir, 0755, true);
        }

        $validFile = $processedDir.'valid_'.$this->fileName;
        $invalidFile = $processedDir.'invalid_'.$this->fileName;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (! $lines) {
            return;
        }

        $validator = new EmailValidator;
        $validation = new MultipleValidationWithAnd([new RFCValidation, new DNSCheckValidation]);

        $validLines = [];
        $invalidLines = [];

        foreach ($lines as $line) {
            $email = trim(explode(';', $line)[0]);
            if ($email === '') {
                continue;
            }

            $isValid = false;

            try {
                $isValid = $validator->isValid($email, $validation);

                // Optional SMTP validation
                if ($isValid && class_exists('\SMTPValidateEmail\Validator')) {
                    try {
                        $smtpValidator = new Validator($email, 'info@icslegal.com');
                        $smtpValidator->debug = false;
                        $smtpResult = $smtpValidator->validate();
                        if (! $smtpResult) {
                            $isValid = false;
                        }
                    } catch (\Exception $smtpEx) {
                        $isValid = false;
                    }
                }

                if ($isValid) {
                    $validLines[] = $line;
                } else {
                    $invalidLines[] = $line;
                }

            } catch (\Exception $e) {
                $invalidLines[] = $line;
            }
        }

        // Save results
        if (! empty($validLines)) {
            file_put_contents($validFile, implode(PHP_EOL, $validLines).PHP_EOL);
        }
        if (! empty($invalidLines)) {
            file_put_contents($invalidFile, implode(PHP_EOL, $invalidLines).PHP_EOL);
        }

        // Send email with file links
        $this->sendResultEmail($validFile, $invalidFile);
    }

    /**
     * Send email with download links
     */
    private function sendResultEmail($validFilePath, $invalidFilePath)
    {
        $validUrl = url('storage/uploads/processed/'.basename($validFilePath));
        $invalidUrl = url('storage/uploads/processed/'.basename($invalidFilePath));
        $subject = "Email Validation Results for {$this->fileName}";
        $content = "
            <p>Hi,</p>
            <p>The email validation process has completed. You can download the results here:</p>
            <ul>
                <li><a href='{$validUrl}' download='valid_emails.txt'>Download Valid Emails</a></li>
                <li><a href='{$invalidUrl}' download='invalid_emails.txt'>Download Invalid Emails</a></li>
            </ul>
            <p>Regards,<br>ICS Legal</p>
        ";

        $result = $this->send_email('ak@sk-associates.org', $subject, $content);
        if (empty($result)) {
            \Log::info('Email sent successfully');
        } else {
            \Log::info('Failed to send email');
        }
    }

    /**
     * Send email using SendGrid
     */
    private function send_email($email, $subject, $content)
    {
        $data = [
            'personalizations' => [
                [
                    'to' => [['email' => $email]],
                    'subject' => $subject,
                ],
            ],
            'content' => [['type' => 'text/html', 'value' => $content]],
            'from' => ['email' => 'icslegaladvice@gmail.com', 'name' => 'ICS Legal'],
            'reply_to' => ['email' => 'info@icslegal.com', 'name' => 'ICS Legal'],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('SENDGRID_APIENDPOINT'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.env('SENDGRID_APIKEY'),
            'Content-Type: application/json',
        ]);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            \Log::error('SendGrid Error: '.curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
}
