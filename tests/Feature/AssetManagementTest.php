<?php

use App\Models\Admin;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    Storage::fake('public');
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

test('deleting a file asset physically deletes it from storage and database', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);
    $path = $file->store('assets', 'public');

    $asset = Asset::create([
        'name' => 'document.pdf',
        'path' => $path,
        'directory' => '',
        'mime' => 'application/pdf',
        'size' => 100,
        'is_directory' => false,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = deleteJson(route('admin.assets.destroy', $asset));

    $response->assertOk();
    $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    Storage::disk('public')->assertMissing($path);
});

test('deleting a directory asset recursively deletes its files and folder from storage and database', function () {
    $dirAsset = Asset::create([
        'name' => 'documents',
        'path' => 'assets/documents',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $file = UploadedFile::fake()->create('report.pdf', 200);
    $filePath = $file->store('assets/documents', 'public');

    $fileAsset = Asset::create([
        'name' => 'report.pdf',
        'path' => $filePath,
        'directory' => 'documents',
        'mime' => 'application/pdf',
        'size' => 200,
        'is_directory' => false,
    ]);

    Storage::disk('public')->assertExists($filePath);

    $response = deleteJson(route('admin.assets.destroy', $dirAsset));

    $response->assertOk();
    $this->assertDatabaseMissing('assets', ['id' => $dirAsset->id]);
    $this->assertDatabaseMissing('assets', ['id' => $fileAsset->id]);
    Storage::disk('public')->assertMissing($filePath);
});

test('deleting a parent directory asset recursively deletes nested subdirectories and all nested files', function () {
    $parentDir = Asset::create([
        'name' => 'parent',
        'path' => 'assets/parent',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $subDir = Asset::create([
        'name' => 'sub',
        'path' => 'parent/sub',
        'directory' => 'parent',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $nestedFile = UploadedFile::fake()->create('deep.pdf', 300);
    $nestedPath = $nestedFile->store('assets/parent/sub', 'public');

    $nestedFileAsset = Asset::create([
        'name' => 'deep.pdf',
        'path' => $nestedPath,
        'directory' => 'parent/sub',
        'mime' => 'application/pdf',
        'size' => 300,
        'is_directory' => false,
    ]);

    Storage::disk('public')->assertExists($nestedPath);

    $response = deleteJson(route('admin.assets.destroy', $parentDir));

    $response->assertOk();
    $this->assertDatabaseMissing('assets', ['id' => $parentDir->id]);
    $this->assertDatabaseMissing('assets', ['id' => $subDir->id]);
    $this->assertDatabaseMissing('assets', ['id' => $nestedFileAsset->id]);
    Storage::disk('public')->assertMissing($nestedPath);
});

test('deleting a directory asset deletes all image files inside it from storage and database', function () {
    $galleryDir = Asset::create([
        'name' => 'gallery',
        'path' => 'assets/gallery',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $imageFile = UploadedFile::fake()->create('photo.jpg', 1500, 'image/jpeg');
    $imagePath = $imageFile->store('assets/gallery', 'public');

    $imageAsset = Asset::create([
        'name' => 'photo.jpg',
        'path' => $imagePath,
        'directory' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1500,
        'width' => 800,
        'height' => 600,
        'is_directory' => false,
    ]);

    Storage::disk('public')->assertExists($imagePath);

    $response = deleteJson(route('admin.assets.destroy', $galleryDir));

    $response->assertOk();
    $this->assertDatabaseMissing('assets', ['id' => $galleryDir->id]);
    $this->assertDatabaseMissing('assets', ['id' => $imageAsset->id]);
    Storage::disk('public')->assertMissing($imagePath);
});

test('deleting a directory asset deletes all zip and video files inside it from storage and database', function () {
    $mediaDir = Asset::create([
        'name' => 'media',
        'path' => 'assets/media',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $zipFile = UploadedFile::fake()->create('archive.zip', 5000, 'application/zip');
    $zipPath = $zipFile->store('assets/media', 'public');

    $zipAsset = Asset::create([
        'name' => 'archive.zip',
        'path' => $zipPath,
        'directory' => 'media',
        'mime' => 'application/zip',
        'size' => 5000,
        'is_directory' => false,
    ]);

    $videoFile = UploadedFile::fake()->create('clip.mp4', 10000, 'video/mp4');
    $videoPath = $videoFile->store('assets/media', 'public');

    $videoAsset = Asset::create([
        'name' => 'clip.mp4',
        'path' => $videoPath,
        'directory' => 'media',
        'mime' => 'video/mp4',
        'size' => 10000,
        'is_directory' => false,
    ]);

    Storage::disk('public')->assertExists($zipPath);
    Storage::disk('public')->assertExists($videoPath);

    $response = deleteJson(route('admin.assets.destroy', $mediaDir));

    $response->assertOk();
    $this->assertDatabaseMissing('assets', ['id' => $mediaDir->id]);
    $this->assertDatabaseMissing('assets', ['id' => $zipAsset->id]);
    $this->assertDatabaseMissing('assets', ['id' => $videoAsset->id]);
    Storage::disk('public')->assertMissing($zipPath);
    Storage::disk('public')->assertMissing($videoPath);
});

test('sub-directory creation sets path prefixed with assets/', function () {
    $parentDir = Asset::create([
        'name' => 'docs',
        'path' => 'assets/docs',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $response = postJson(route('admin.assets.directory'), [
        'name' => 'subfolder',
        'directory' => 'docs',
    ]);

    $response->assertOk();
    $subDir = Asset::where('name', 'subfolder')->where('directory', 'docs')->first();
    expect($subDir)->not->toBeNull();
    expect($subDir->path)->toBe('assets/docs/subfolder');
    Storage::disk('public')->assertExists('assets/docs/subfolder');
});

test('uploading document inside directory and renaming directory keeps assets accessible', function () {
    $dirAsset = Asset::create([
        'name' => 'docs',
        'path' => 'assets/docs',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $file = UploadedFile::fake()->create('report.pdf', 500, 'application/pdf');
    $uploadResponse = postJson(route('admin.assets.store'), [
        'file' => $file,
        'directory' => 'docs',
    ]);
    $uploadResponse->assertOk();

    $docAsset = Asset::where('directory', 'docs')->where('name', 'report.pdf')->first();
    expect($docAsset)->not->toBeNull();
    Storage::disk('public')->assertExists($docAsset->path);

    // Rename directory 'docs' to 'documents'
    $renameResponse = putJson(route('admin.assets.update', $dirAsset), [
        'name' => 'documents',
    ]);
    $renameResponse->assertOk();

    $docAsset->refresh();
    expect($docAsset->directory)->toBe('documents');
    expect($docAsset->path)->toBe('assets/documents/'.basename($docAsset->path));
    Storage::disk('public')->assertExists($docAsset->path);

    // Assert list endpoint lists the asset inside 'documents'
    $indexResponse = getJson(route('admin.assets.list', ['directory' => 'documents']));
    $indexResponse->assertOk();
    $assets = $indexResponse->json('assets');
    expect(collect($assets)->pluck('id'))->toContain($docAsset->id);
});

test('renaming a parent directory containing nested directories and files updates child directory paths correctly', function () {
    $parentDir = Asset::create([
        'name' => 'parent',
        'path' => 'assets/parent',
        'directory' => '',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $subDir = Asset::create([
        'name' => 'sub',
        'path' => 'assets/parent/sub',
        'directory' => 'parent',
        'mime' => 'directory',
        'size' => 0,
        'is_directory' => true,
    ]);

    $file = UploadedFile::fake()->create('nested.pdf', 300, 'application/pdf');
    $filePath = $file->store('assets/parent/sub', 'public');

    $fileAsset = Asset::create([
        'name' => 'nested.pdf',
        'path' => $filePath,
        'directory' => 'parent/sub',
        'mime' => 'application/pdf',
        'size' => 300,
        'is_directory' => false,
    ]);

    // Rename parent directory from 'parent' to 'renamed_parent'
    $renameResponse = putJson(route('admin.assets.update', $parentDir), [
        'name' => 'renamed_parent',
    ]);
    $renameResponse->assertOk();

    $subDir->refresh();
    $fileAsset->refresh();

    expect($subDir->directory)->toBe('renamed_parent');
    expect($subDir->path)->toBe('assets/renamed_parent/sub');

    expect($fileAsset->directory)->toBe('renamed_parent/sub');
    expect($fileAsset->path)->toBe('assets/renamed_parent/sub/'.basename($filePath));

    Storage::disk('public')->assertExists($fileAsset->path);
});
