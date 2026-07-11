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
            ->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'path' => $asset->is_directory ? ($asset->directory ? $asset->directory.'/'.$asset->name : $asset->name) : $asset->path,
                    'size' => $asset->is_directory ? null : $this->formatSize($asset->size),
                    'width' => $asset->width,
                    'height' => $asset->height,
                    'mime_type' => $asset->mime,
                    'is_directory' => $asset->is_directory,
                    'created_at' => $asset->created_at->toIso8601String(),
                    'updated_at' => $asset->updated_at->toIso8601String(),
                ];
            });

        return response()->json(['assets' => $assets]);
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
                $oldPath = $asset->path;
                $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = ($asset->directory ? $asset->directory.'/' : 'assets/').$name;
                if ($ext && ! str_ends_with($newPath, '.'.$ext)) {
                    $newPath .= '.'.$ext;
                }
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->move($oldPath, $newPath);
                }
                $asset->update(['name' => $name, 'path' => $newPath]);
            }
        }

        if ($request->has('directory')) {
            $asset->update(['directory' => $request->directory]);
        }

        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        if ($asset->is_directory) {
            $prefix = $asset->path;
            $children = Asset::where('path', 'like', $prefix.'/%')
                ->orWhere('path', $prefix)
                ->where('id', '!=', $asset->id)
                ->get();
            foreach ($children as $child) {
                if ($child->is_directory) {
                    $this->destroyDirectory($child);
                } else {
                    Storage::disk('public')->delete($child->path);
                    $child->delete();
                }
            }
            Storage::disk('public')->deleteDirectory($prefix);
            $asset->delete();
        } else {
            Storage::disk('public')->delete($asset->path);
            $asset->delete();
        }

        return response()->json(['message' => 'Deleted.']);
    }

    private function destroyDirectory(Asset $dir)
    {
        $children = Asset::where('path', 'like', $dir->path.'/%')
            ->orWhere('path', $dir->path)
            ->where('id', '!=', $dir->id)
            ->get();
        foreach ($children as $child) {
            if ($child->is_directory) {
                $this->destroyDirectory($child);
            } else {
                Storage::disk('public')->delete($child->path);
                $child->delete();
            }
        }
        Storage::disk('public')->deleteDirectory($dir->path);
        $dir->delete();
    }

    public function file(Asset $asset)
    {
        if ($asset->is_directory) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($asset->path));
    }

    public function directory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'directory' => 'nullable|string',
        ]);

        $name = trim($request->name);
        $parentDir = $request->input('directory', '') ?? '';

        $exists = Asset::where('name', $name)
            ->where('directory', $parentDir)
            ->where('is_directory', true)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'A directory with that name already exists.'], 422);
        }

        $dirPath = $parentDir ? $parentDir.'/'.$name : 'assets/'.$name;
        Storage::disk('public')->makeDirectory($dirPath);

        $asset = Asset::create([
            'name' => $name,
            'path' => $dirPath,
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
