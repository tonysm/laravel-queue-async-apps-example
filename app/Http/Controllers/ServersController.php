<?php

namespace App\Http\Controllers;

use App\Events\ServerWasCreated;
use App\Server;
use Illuminate\Http\Request;

class ServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request->user()
            ->servers()
            ->with('user')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $server = $request->user()->servers()->create([
            'name' => $request->get('name'),
            'status' => 'creating',
        ]);

        event(new ServerWasCreated($server));

        return $server->load('user');
    }
}