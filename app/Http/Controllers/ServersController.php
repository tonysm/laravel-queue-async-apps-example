<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;
use App\Events\ServerWasCreated;

class ServersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
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

        return response()->json($server->load('user'), 201);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $serverId
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, string $serverId)
    {
        $server = $request->user()->servers()->findOrFail($serverId);

        if (!in_array($server->status, ['ready', 'failed'])) {
            throw new \Exception('Not in a good state to be deleted, buddy.');
        }

        $server->delete();

        return response()->json([], 204);
    }
}
