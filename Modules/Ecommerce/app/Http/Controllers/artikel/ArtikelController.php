<?php

namespace Modules\Ecommerce\Http\Controllers\artikel;

use App\Http\Controllers\Controller;
use Modules\Ecommerce\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\HasMiddleware; 
use Illuminate\Routing\Controllers\Middleware;

class ArtikelController extends Controller implements HasMiddleware
{
    
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-artikel', only: ['index', 'show']),
            new Middleware('permission:create-artikel', only: ['create', 'store']),
            new Middleware('permission:edit-artikel', only: ['edit', 'update']),
            new Middleware('permission:delete-artikel', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Artikel::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('judul_artikel', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('kategori')) {
            $query->whereJsonContains('kategori', $request->input('kategori'));
        }

        $artikels = $query->paginate(10)->withQueryString();

        return view('ecommerce::artikel.index', compact('artikels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ecommerce::artikel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'isi_artikel'   => 'required|string',
            'status'        => 'required|in:draft,published',
            'kategori'      => 'nullable|array',
            'kategori.*'    => 'string|max:100',
            'thumbnail'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Generate slug unik
        $slug = Str::slug($validated['judul_artikel']);
        $originalSlug = $slug;
        $counter = 1;
        while (Artikel::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('artikels/thumbnails', 'r2');
        }

        $validated['user_id']  = Auth::id();
        $validated['kategori'] = $request->input('kategori', []);

        Artikel::create($validated);

        return redirect()->route('artikel.index')
            ->with('success', 'Artikel berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Artikel $artikel)
    {
        return view('ecommerce::artikel.show', compact('artikel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artikel $artikel)
    {
        return view('ecommerce::artikel.edit', compact('artikel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artikel $artikel)
    {
        $validated = $request->validate([
            'judul_artikel' => 'required|string|max:255',
            'isi_artikel'   => 'required|string',
            'status'        => 'required|in:draft,published',
            'kategori'      => 'nullable|array',
            'kategori.*'    => 'string|max:100',
            'thumbnail'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Regenerate slug hanya jika judul berubah
        if ($artikel->judul_artikel !== $validated['judul_artikel']) {
            $slug = Str::slug($validated['judul_artikel']);
            $originalSlug = $slug;
            $counter = 1;
            while (Artikel::where('slug', $slug)->where('id', '!=', $artikel->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Upload thumbnail baru (hapus yang lama jika ada)
        if ($request->hasFile('thumbnail')) {
            if ($artikel->thumbnail) {
                Storage::disk('r2')->delete($artikel->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('artikels/thumbnails', 'r2');
        }

        // Hapus thumbnail jika diminta
        if ($request->input('remove_thumbnail') == '1') {
            if ($artikel->thumbnail) {
                Storage::disk('r2')->delete($artikel->thumbnail);
            }
            $validated['thumbnail'] = null;
        }

        $validated['user_id']  = Auth::id();
        $validated['kategori'] = $request->input('kategori', []);

        $artikel->update($validated);

        return redirect()->route('artikel.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artikel $artikel)
    {
        if ($artikel->thumbnail) {
            Storage::disk('r2')->delete($artikel->thumbnail);
        }

        $artikel->delete();

        return redirect()->route('artikel.index')
            ->with('success', 'Artikel berhasil dihapus!');
    }
}
