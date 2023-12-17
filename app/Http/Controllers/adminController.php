<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;




class adminController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $admins = admin::latest()->paginate(10);
        return view('admin.index', compact('admins'));
    }


    public function create()
    {
        return view('admin.create');
    }


    /**
    * store
    *
    * @param  mixed $request
    * @return void
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'title'     => 'required',
            'content'   => 'required',
           ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/admins', $image->hashName());

        $admin = admin::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
            'updated_at'   => $request->updated_at,
            'created_at'   => $request->created_at
        ]);

        if($admin){
            //redirect dengan pesan sukses
            return redirect()->route('admin.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('admin.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

 
public function edit(admin $admin)
{
    return view('admin.edit', compact('admin'));
}


public function update(Request $request, admin $admin)
{
    $this->validate($request, [
        'title'     => 'required',
        'content'   => 'required'
    ]);

    //get data Blog by ID
    $blog = admin::findOrFail($admin->id);

    if($request->file('image') == "") {

        $admin->update([
            'title'     => $request->title,
            'content'   => $request->content
        ]);

    } else {

        //hapus old image
        Storage::disk('local')->delete('public/admins/'.$admin->image);

        //upload new image
        $image = $request->file('image');
        $image->storeAs('public/admins', $image->hashName());

        $admin->update([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

    }

    if($admin){
        //redirect dengan pesan sukses
        return redirect()->route('admin.index')->with(['success' => 'Data Berhasil Diupdate!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('admin.index')->with(['error' => 'Data Gagal Diupdate!']);
    }
}

public function destroy($id)
{
  $admin = admin::findOrFail($id);
  Storage::disk('local')->delete('public/admins/'.$admin->image);
  $admin->delete();

  if($admin){
     //redirect dengan pesan sukses
     return redirect()->route('admin.index')->with(['success' => 'Data Berhasil Dihapus!']);
  }else{
    //redirect dengan pesan error
    return redirect()->route('admin.index')->with(['error' => 'Data Gagal Dihapus!']);
  }
}

        
    }
