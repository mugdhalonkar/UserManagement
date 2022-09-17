<?php

namespace App\Http\Controllers;

use App\Models\User;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(user::select('*'))
                ->addColumn('action', 'user-action')
                ->addColumn('image', 'image')
                ->rawColumns(['action', 'image'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('user-list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = $request->id;
        if ($userId) {

            $user = User::find($userId);
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $user->image = $path;
            }
        } else {
            $path = $request->file('image')->store('public/images');
            $user = new user;
            $user->image = $path;
        }
        $start = date_create($request->date_of_joining);
        $end = date_create(!empty($request->date_of_leaving) ? $request->date_of_leaving : date("Y-m-d"));
        $diff = date_diff($start, $end);
        $experience = $diff->y . " years " . $diff->m . " months";
        $user->name = $request->name;
        $user->email = $request->email;
        $user->experience = $experience;
        $user->date_of_joining = $start;
        $user->date_of_leaving = $request->date_of_leaving;
        $user->save();

        return Response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $user = user::where($where)->first();

        return Response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $imageResp = user::select('image')->where('id', $request->id)->first();
        $user = user::where('id', $request->id)->delete();
        if (!empty($imageResp)) {
            $path = $imageResp->image;
            $newpath = str_replace("public/", "", $path);
            File::delete(public_path('storage/' . $newpath));
        }
        // return Response()->json($user);
    }
}
