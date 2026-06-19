<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('prioritas', 'desc')
            ->orderBy('kategori')
            ->paginate(20);

        return view('faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('faq.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'kategori' => 'required|string|max:100',
            'prioritas' => 'required|integer|min:0|max:10'
        ]);

        $validated['aktif'] = $request->has('aktif');

        Faq::create($validated);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(Faq $faq)
    {
        return view('faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'kategori' => 'required|string|max:100',
            'prioritas' => 'required|integer|min:0|max:10'
        ]);

        $validated['aktif'] = $request->has('aktif');

        $faq->update($validated);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}