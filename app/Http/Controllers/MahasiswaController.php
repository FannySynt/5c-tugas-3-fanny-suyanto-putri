<?php

namespace App\Http\Controllers;

use App\Models\mahasiswa;
use Faker\Core\File;
use Faker\Provider\File as ProviderFile;
use Illuminate\Http\File as HttpFile;
use Illuminate\Http\Request;
use Illuminate\Http\Testing\File as TestingFile;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File as RulesFile;

class MahasiswaController extends Controller
{
    public function index()
    {
        $data = mahasiswa::get();
        return view('mahasiswa.index', ['mahasiswas' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('mahasiswa/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'npm' => 'required|unique:mahasiswas',
            'nama' => 'required',
            'alamat' => 'required',
            'ttl' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->hasFile('photo')) {
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . $request->nama . '-' . time() . '.' . $extension;

            $path = $request->file('photo')->storeAs('public', $filenameSimpan);
        }

        $mahasiswa = new mahasiswa;
        $mahasiswa->npm = $request->input('npm');
        $mahasiswa->nama = $request->input('nama');
        $mahasiswa->jenis_kelamin = $request->input('jenis_kelamin');
        $mahasiswa->alamat = $request->input('alamat');
        $mahasiswa->ttl = $request->input('ttl');
        $mahasiswa->photo = $filenameSimpan;
        $mahasiswa->save();

        return redirect()->route('mahasiswa.index')->with('message', "Data {$request['nama']} berhasil ditambahkan!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(mahasiswa $mahasiswa)
    {
        return view('mahasiswa.detail', [
            'mahasiswa' => $mahasiswa,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = mahasiswa::find($id);
        return view('mahasiswa.edit', [
            'mahasiswa' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'npm' => 'required|unique:mahasiswas',
            'nama' => 'required',
            'alamat' => 'required',
            'ttl' => 'required',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $mahasiswa = mahasiswa::find($id);
        $mahasiswa->npm = $request->input('npm');
        $mahasiswa->nama = $request->input('nama');
        $mahasiswa->jenis_kelamin = $request->input('jenis_kelamin');
        $mahasiswa->alamat = $request->input('alamat');
        $mahasiswa->ttl = $request->input('ttl');

        if ($request->hasFile('photo')) {

            $destination = 'public' . $request->gambar;
            if (RulesFile::exists($destination)) {
                RulesFile::delete($destination);
            }

            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo')->getClientOriginalExtension();
            $filenameSimpan = $filename . '_' . $request->nama . '-' . time() . '.' . $extension;

            $path = $request->file('photo')->storeAs('public', $filenameSimpan);
            $mahasiswa->photo = $filenameSimpan;
        }

        $mahasiswa->update();

        return redirect()->route('mahasiswa.index')->with('message', "Data mahasiswa berhasil diubah!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mahasiswa = mahasiswa::find($id);
        if ($mahasiswa->photo) {
            Storage::delete($mahasiswa->photo);
        }
        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('message', "Data {$mahasiswa['nama']} berhasil dihapus!");
    }
}