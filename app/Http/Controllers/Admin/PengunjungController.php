<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengunjung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PengunjungController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengunjung::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $pengunjungs = $query->latest()->paginate(15)->withQueryString();
        
        return view('admin.pengunjung.index', compact('pengunjungs'));
    }

    public function edit(Pengunjung $pengunjung)
    {
        return view('admin.pengunjung.edit', compact('pengunjung'));
    }

    public function update(Request $request, Pengunjung $pengunjung)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pengunjungs')->ignore($pengunjung->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $pengunjung->update($dataToUpdate);

        return redirect()->route('admin.pengunjung.index')->with('success', 'Data pengunjung berhasil diperbarui.');
    }

    public function destroy(Pengunjung $pengunjung)
    {
        $pengunjung->delete();

        return redirect()->route('admin.pengunjung.index')->with('success', 'Pengunjung berhasil dihapus.');
    }
}
