<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class CloudflareR2Service
{
    public ?string $accountId;

    public ?string $accessKeyId;

    public ?string $secretAccessKey;

    public ?string $bucket;

    public ?string $publicUrl;

    public bool $enabled;

    public function __construct(?array $credentials = null)
    {
        if ($credentials) {
            $this->accountId = trim((string) ($credentials['account_id'] ?? ''));
            $this->accessKeyId = trim((string) ($credentials['access_key_id'] ?? ''));
            $this->secretAccessKey = trim((string) ($credentials['secret_access_key'] ?? ''));
            $this->bucket = trim((string) ($credentials['bucket'] ?? ''));
            $this->publicUrl = trim((string) ($credentials['public_url'] ?? ''));
            $this->enabled = (bool) ($credentials['enabled'] ?? true);
        } else {
            $setting = Setting::first();
            $this->accountId = trim((string) ($setting?->cloudflare_r2_account_id ?: config('filesystems.disks.r2.account_id', env('CLOUDFLARE_R2_ACCOUNT_ID'))));
            $this->accessKeyId = trim((string) ($setting?->cloudflare_r2_access_key_id ?: config('filesystems.disks.r2.key', env('CLOUDFLARE_R2_ACCESS_KEY_ID'))));
            $this->secretAccessKey = trim((string) ($setting?->cloudflare_r2_secret_access_key ?: config('filesystems.disks.r2.secret', env('CLOUDFLARE_R2_SECRET_ACCESS_KEY'))));
            $this->bucket = trim((string) ($setting?->cloudflare_r2_bucket ?: config('filesystems.disks.r2.bucket', env('CLOUDFLARE_R2_BUCKET'))));
            $this->publicUrl = trim((string) ($setting?->cloudflare_r2_public_url ?: config('filesystems.disks.r2.url', env('CLOUDFLARE_R2_PUBLIC_URL'))));
            $this->enabled = (bool) ($setting?->cloudflare_r2_enabled ?? false);
        }
    }

    /**
     * Check if R2 is actively enabled and has valid credentials.
     */
    public function isEnabled(): bool
    {
        return $this->enabled && $this->isConfigured();
    }

    /**
     * Check if credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->accountId) && ! empty($this->accessKeyId) && ! empty($this->secretAccessKey) && ! empty($this->bucket);
    }

    public function getBucket(): ?string
    {
        return $this->bucket;
    }

    public function getPublicUrl(): ?string
    {
        return $this->publicUrl ? rtrim($this->publicUrl, '/') : null;
    }

    /**
     * Get public URL for an asset key.
     */
    public function getUrl(string $path): string
    {
        $cleanPath = ltrim($path, '/');
        if (! empty($this->publicUrl)) {
            return rtrim($this->publicUrl, '/').'/'.$cleanPath;
        }

        return '/storage/'.$cleanPath;
    }

    /**
     * Upload an UploadedFile instance to R2.
     */
    public function uploadFile(string $path, UploadedFile $file): bool
    {
        $contents = file_get_contents($file->getRealPath());
        $mime = $file->getMimeType() ?: 'application/octet-stream';

        return $this->put($path, $contents, $mime);
    }

    /**
     * Upload raw binary content to R2.
     */
    public function put(string $path, string $contents, ?string $mime = null): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $cleanPath = ltrim($path, '/');
        $mimeType = $mime ?: $this->detectMimeType($cleanPath);

        $response = $this->sendRequest('PUT', "/{$this->bucket}/{$cleanPath}", [], $contents, [
            'content-type' => $mimeType,
        ]);

        return $response->successful();
    }

    /**
     * Get content from R2.
     */
    public function get(string $path): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $cleanPath = ltrim($path, '/');
        $response = $this->sendRequest('GET', "/{$this->bucket}/{$cleanPath}");

        return $response->successful() ? $response->body() : null;
    }

    /**
     * Check if object exists on R2.
     */
    public function exists(string $path): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $cleanPath = ltrim($path, '/');
        $response = $this->sendRequest('HEAD', "/{$this->bucket}/{$cleanPath}");

        return $response->successful();
    }

    /**
     * Delete an object from R2.
     */
    public function delete(string $path): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $cleanPath = ltrim($path, '/');
        $response = $this->sendRequest('DELETE', "/{$this->bucket}/{$cleanPath}");

        return $response->successful() || $response->status() === 404;
    }

    /**
     * Delete all objects with given prefix directory from R2.
     */
    public function deleteDirectory(string $prefix): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $cleanPrefix = rtrim(ltrim($prefix, '/'), '/').'/';
        $objects = $this->listObjects($cleanPrefix);

        foreach ($objects as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * Move / Rename an object in R2.
     */
    public function move(string $oldPath, string $newPath): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $cleanOld = ltrim($oldPath, '/');
        $cleanNew = ltrim($newPath, '/');

        // Copy object to new key using x-amz-copy-source header
        $copySource = rawurlencode("{$this->bucket}/{$cleanOld}");
        $response = $this->sendRequest('PUT', "/{$this->bucket}/{$cleanNew}", [], '', [
            'x-amz-copy-source' => $copySource,
        ]);

        if ($response->successful()) {
            $this->delete($cleanOld);

            return true;
        }

        return false;
    }

    /**
     * List object keys by prefix.
     */
    public function listObjects(string $prefix = '', int $maxKeys = 1000): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $query = ['list-type' => '2'];
        if (! empty($prefix)) {
            $query['prefix'] = $prefix;
        }
        if ($maxKeys > 0) {
            $query['max-keys'] = (string) $maxKeys;
        }

        $response = $this->sendRequest('GET', "/{$this->bucket}", $query);
        if (! $response->successful()) {
            return [];
        }

        $xml = @simplexml_load_string($response->body());
        if (! $xml) {
            return [];
        }

        $keys = [];
        if (isset($xml->Contents)) {
            foreach ($xml->Contents as $item) {
                if (isset($item->Key)) {
                    $keys[] = (string) $item->Key;
                }
            }
        }

        return $keys;
    }

    /**
     * Test connection to Cloudflare R2 bucket.
     */
    public function testConnection(?array $customCreds = null): array
    {
        $service = $customCreds ? new self($customCreds) : $this;

        if (! $service->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Please fill in all required Cloudflare R2 credentials (Account ID, Access Key, Secret Key, and Bucket Name).',
            ];
        }

        try {
            $response = $service->sendRequest('GET', "/{$service->bucket}", [
                'list-type' => '2',
                'max-keys' => '1',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => "Successfully connected to Cloudflare R2 bucket '{$service->bucket}'!",
                ];
            }

            $status = $response->status();
            $body = $response->body();
            $errMessage = "HTTP {$status}";

            if (preg_match('/<Message>(.*?)<\/Message>/s', $body, $matches)) {
                $errMessage = $matches[1];
            } elseif (preg_match('/<Code>(.*?)<\/Code>/s', $body, $matches)) {
                $errMessage = $matches[1];
            }

            if ($status === 403 || $status === 401) {
                $errMessage .= ' (Authentication failed. Check your Access Key ID and Secret Access Key).';
            } elseif ($status === 404) {
                $errMessage .= " (Bucket '{$service->bucket}' was not found in account '{$service->accountId}').";
            }

            return [
                'success' => false,
                'message' => "Connection failed: {$errMessage}",
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Connection error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send signed AWS SigV4 request to Cloudflare R2.
     */
    protected function sendRequest(
        string $method,
        string $uriPath,
        array $query = [],
        string $body = '',
        array $extraHeaders = []
    ): Response {
        $host = "{$this->accountId}.r2.cloudflarestorage.com";
        $endpoint = "https://{$host}{$uriPath}";

        $amzDate = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');
        $region = 'auto';
        $service = 's3';

        $payloadHash = hash('sha256', $body);

        $headers = array_merge([
            'host' => $host,
            'x-amz-date' => $amzDate,
            'x-amz-content-sha256' => $payloadHash,
        ], $extraHeaders);

        // Normalize header names to lowercase
        $normalizedHeaders = [];
        foreach ($headers as $k => $v) {
            $normalizedHeaders[strtolower(trim($k))] = trim((string) $v);
        }
        ksort($normalizedHeaders);

        // Canonical headers string & signed headers list
        $canonicalHeaders = '';
        $signedHeadersList = [];
        foreach ($normalizedHeaders as $k => $v) {
            $canonicalHeaders .= "{$k}:{$v}\n";
            $signedHeadersList[] = $k;
        }
        $signedHeaders = implode(';', $signedHeadersList);

        // Canonical query string (sorted)
        ksort($query);
        $canonicalQueryArr = [];
        foreach ($query as $k => $v) {
            $canonicalQueryArr[] = rawurlencode($k).'='.rawurlencode((string) $v);
        }
        $canonicalQuery = implode('&', $canonicalQueryArr);

        // Canonical URI
        $canonicalUri = $uriPath ?: '/';

        // Canonical Request
        $canonicalRequest = strtoupper($method)."\n".
            $canonicalUri."\n".
            $canonicalQuery."\n".
            $canonicalHeaders."\n".
            $signedHeaders."\n".
            $payloadHash;

        // String to sign
        $algorithm = 'AWS4-HMAC-SHA256';
        $credentialScope = "{$dateStamp}/{$region}/{$service}/aws4_request";
        $stringToSign = $algorithm."\n".
            $amzDate."\n".
            $credentialScope."\n".
            hash('sha256', $canonicalRequest);

        // Signature derivation
        $kDate = hash_hmac('sha256', $dateStamp, 'AWS4'.$this->secretAccessKey, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', $service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        // Authorization header
        $authorization = "{$algorithm} Credential={$this->accessKeyId}/{$credentialScope}, SignedHeaders={$signedHeaders}, Signature={$signature}";

        $requestHeaders = $normalizedHeaders;
        $requestHeaders['authorization'] = $authorization;

        $url = $endpoint.($canonicalQuery ? '?'.$canonicalQuery : '');

        $client = Http::withHeaders($requestHeaders)->timeout(30);

        if (in_array(strtoupper($method), ['PUT', 'POST', 'PATCH'])) {
            $client->withBody($body, $normalizedHeaders['content-type'] ?? 'application/octet-stream');
        }

        return $client->send($method, $url);
    }

    protected function detectMimeType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };
    }
}
