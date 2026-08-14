<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetsController extends Controller
{
    public function index(Request $request)
    {
        $directory = $request->query('directory', '');

        $assets = Asset::where('directory', $directory)
            ->orderBy('is_directory', 'desc')
            ->orderBy('name')
            ->get()
            ->filter(function ($asset) {
                // Remove orphaned records where the physical file no longer exists on disk.
                if ($asset->is_directory) {
                    return true;
                }

                return Storage::disk('public')->exists($asset->path);
            })
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    // Directories do not have a public storage URL; returning null prevents
                    // the browser from firing a spurious /storage/<dirname> network request.
                    'path' => $asset->is_directory ? null : $asset->path,
                    // Keep the directory navigation path separate so the frontend can
                    // use it to navigate into the directory without building a file URL.
                    'directory_path' => $asset->is_directory
                        ? ($asset->directory ? $asset->directory.'/'.$asset->name : $asset->name)
                        : null,
                    'size' => $asset->is_directory ? null : $this->formatSize($asset->size),
                    'raw_size' => $asset->is_directory ? 0 : (int) $asset->size,
                    'width' => $asset->width,
                    'height' => $asset->height,
                    'mime_type' => $asset->mime,
                    'is_directory' => $asset->is_directory,
                    'created_at' => $asset->created_at->toIso8601String(),
                    'updated_at' => $asset->updated_at->toIso8601String(),
                ];
            })->values();

        $totalStorageBytes = (int) Asset::where('is_directory', false)->sum('size');
        $diskTotalBytes = @disk_total_space(storage_path('app/public')) ?: (5 * 1024 * 1024 * 1024);

        return response()->json([
            'assets' => $assets,
            'total_storage' => $this->formatSize($totalStorageBytes),
            'total_storage_bytes' => $totalStorageBytes,
            'capacity_storage' => $this->formatSize($diskTotalBytes),
            'capacity_storage_bytes' => $diskTotalBytes,
            'storage_display' => $this->formatSize($totalStorageBytes).' / '.$this->formatSize($diskTotalBytes),
        ]);
    }

    public function page()
    {
        return view('admin.assets.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400',
            'directory' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $directory = $request->input('directory', '') ?? '';
        $prefix = $directory ? 'assets/'.$directory : 'assets';
        $path = $file->store($prefix, 'public');

        [$width, $height] = $this->getImageDimensions($file);

        $asset = Asset::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'directory' => $directory,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
        ]);

        return response()->json($asset);
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'directory' => 'nullable|string',
        ]);

        if ($request->has('name')) {
            $name = trim($request->name);
            if ($name && $name !== $asset->name) {
                if ($asset->is_directory) {
                    $oldDirPath = $asset->directory ? $asset->directory.'/'.$asset->name : $asset->name;
                    $newDirPath = $asset->directory ? $asset->directory.'/'.$name : $name;
                    $oldStoragePath = 'assets/'.$oldDirPath;
                    $newStoragePath = 'assets/'.$newDirPath;

                    if ($oldStoragePath !== $newStoragePath) {
                        if (Storage::disk('public')->exists($oldStoragePath)) {
                            Storage::disk('public')->move($oldStoragePath, $newStoragePath);
                        } else {
                            Storage::disk('public')->makeDirectory($newStoragePath);
                        }
                    }

                    $children = Asset::where('directory', $oldDirPath)
                        ->orWhere('directory', 'like', $oldDirPath.'/%')
                        ->get();

                    foreach ($children as $child) {
                        $childSubPath = substr($child->directory, strlen($oldDirPath));
                        $updatedChildDir = $newDirPath.$childSubPath;

                        if ($child->is_directory) {
                            $child->update([
                                'directory' => $updatedChildDir,
                                'path' => 'assets/'.$updatedChildDir.'/'.$child->name,
                            ]);
                        } else {
                            $fileName = basename($child->path);
                            $child->update([
                                'directory' => $updatedChildDir,
                                'path' => 'assets/'.$updatedChildDir.'/'.$fileName,
                            ]);
                        }
                    }

                    $asset->update([
                        'name' => $name,
                        'path' => 'assets/'.$newDirPath,
                    ]);
                } else {
                    $oldPath = $asset->path;
                    $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $newPath = ($asset->directory ? 'assets/'.$asset->directory.'/' : 'assets/').$name;
                    if ($ext && ! str_ends_with($newPath, '.'.$ext)) {
                        $newPath .= '.'.$ext;
                    }
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->move($oldPath, $newPath);
                    }
                    $asset->update(['name' => $name, 'path' => $newPath]);
                }
            }
        }

        if ($request->has('directory')) {
            $newDir = trim($request->directory ?? '', '/');

            if ($asset->is_directory) {
                $oldDirPath = $asset->directory ? $asset->directory.'/'.$asset->name : $asset->name;

                if ($newDir !== $asset->directory) {
                    if ($newDir === $oldDirPath || str_starts_with($newDir, $oldDirPath.'/')) {
                        return response()->json(['message' => 'Cannot move a directory into itself or its subdirectories.'], 422);
                    }

                    $newDirPath = $newDir ? $newDir.'/'.$asset->name : $asset->name;
                    $oldStoragePath = 'assets/'.$oldDirPath;
                    $newStoragePath = 'assets/'.$newDirPath;

                    if ($oldStoragePath !== $newStoragePath) {
                        if (Storage::disk('public')->exists($oldStoragePath)) {
                            Storage::disk('public')->move($oldStoragePath, $newStoragePath);
                        } else {
                            Storage::disk('public')->makeDirectory($newStoragePath);
                        }
                    }

                    $children = Asset::where('directory', $oldDirPath)
                        ->orWhere('directory', 'like', $oldDirPath.'/%')
                        ->get();

                    foreach ($children as $child) {
                        $childSubPath = substr($child->directory, strlen($oldDirPath));
                        $updatedChildDir = $newDirPath.$childSubPath;

                        if ($child->is_directory) {
                            $child->update([
                                'directory' => $updatedChildDir,
                                'path' => 'assets/'.$updatedChildDir.'/'.$child->name,
                            ]);
                        } else {
                            $fileName = basename($child->path);
                            $child->update([
                                'directory' => $updatedChildDir,
                                'path' => 'assets/'.$updatedChildDir.'/'.$fileName,
                            ]);
                        }
                    }

                    $asset->update([
                        'directory' => $newDir,
                        'path' => 'assets/'.$newDirPath,
                    ]);
                }
            } else {
                $oldPath = $asset->path;
                $fileName = basename($oldPath);
                $newPath = $newDir ? 'assets/'.$newDir.'/'.$fileName : 'assets/'.$fileName;

                if ($oldPath !== $newPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                }

                $asset->update(['directory' => $newDir, 'path' => $newPath]);
            }
        }

        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        if ($asset->is_directory) {
            $dirPath = $asset->directory ? $asset->directory.'/'.$asset->name : $asset->name;
            $storageDirPath = str_starts_with($asset->path, 'assets/') ? $asset->path : 'assets/'.$dirPath;

            $this->destroyDirectoryChildren($dirPath);

            if (Storage::disk('public')->exists($storageDirPath)) {
                Storage::disk('public')->deleteDirectory($storageDirPath);
            }
            if (Storage::disk('public')->exists('assets/'.$dirPath)) {
                Storage::disk('public')->deleteDirectory('assets/'.$dirPath);
            }

            $asset->delete();
        } else {
            $this->deletePhysicalFile($asset->path);
            $asset->delete();
        }

        return response()->json(['message' => 'Deleted successfully.']);
    }

    private function destroyDirectoryChildren(string $dirPath)
    {
        $children = Asset::where('directory', $dirPath)
            ->orWhere('directory', 'like', $dirPath.'/%')
            ->get();

        foreach ($children as $child) {
            if (! $child->is_directory) {
                $this->deletePhysicalFile($child->path);
            }
            $child->delete();
        }
    }

    private function deletePhysicalFile(?string $path)
    {
        if (! $path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        if (! str_starts_with($path, 'assets/') && Storage::disk('public')->exists('assets/'.$path)) {
            Storage::disk('public')->delete('assets/'.$path);
        }
    }

    public function file(Request $request, Asset $asset)
    {
        if ($asset->is_directory) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($asset->path);

        if (! file_exists($fullPath)) {
            abort(404);
        }

        if ($request->query('download') || $request->has('download')) {
            $name = $asset->name;
            $ext = pathinfo($asset->path, PATHINFO_EXTENSION);
            if ($ext && ! str_ends_with(strtolower($name), '.'.strtolower($ext))) {
                $name .= '.'.$ext;
            }

            return response()->download($fullPath, $name);
        }

        return response()->file($fullPath);
    }

    public function directory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'directory' => 'nullable|string',
        ]);

        $name = trim($request->name);
        $parentDir = trim($request->input('directory', '') ?? '', '/');

        $exists = Asset::where('name', $name)
            ->where('directory', $parentDir)
            ->where('is_directory', true)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'A directory with that name already exists.'], 422);
        }

        $relDirPath = $parentDir ? $parentDir.'/'.$name : $name;
        $storageDirPath = 'assets/'.$relDirPath;

        Storage::disk('public')->makeDirectory($storageDirPath);

        $asset = Asset::create([
            'name' => $name,
            'path' => $storageDirPath,
            'directory' => $parentDir,
            'is_directory' => true,
            'mime' => 'directory',
            'size' => 0,
        ]);

        return response()->json($asset);
    }

    private function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }

    private function getImageDimensions($file): array
    {
        try {
            if (str_starts_with($file->getMimeType(), 'image/')) {
                [$w, $h] = getimagesize($file->getPathname());

                return [$w, $h];
            }
        } catch (\Exception) {
        }

        return [null, null];
    }
}
