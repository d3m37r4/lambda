<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ServersManagementController extends Controller {
    /**
     * Display a listing of the servers.
     *
     * @return Application|Factory|View|Response
     */
    public function index() {
        $servers = Server::paginate(env('PAGINATION_SIZE'));

        return view('admin.servers.index', compact('servers'));
    }

    /**
     * Show the form for creating a new server.
     *
     * @return Application|Factory|View|Response
     */
    public function create() {
        return view('admin.servers.create');
    }

    /**
     * Store a newly created server in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(Request $request): RedirectResponse {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:servers'],
            'ip' => ['required', 'ip'],
            'port' => ['required', 'integer', 'between:1,65535'],
        ];

        $rconCheck = !empty($request->input('rcon'));

        if ($rconCheck) {
            $rules['rcon'] = ['string', 'max:128'];
        }

//        TODO: Add IP+port pair verification
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $server = Server::create([
            'name' => strip_tags($request->input('name')),
            'ip' => $request->input('ip'),
            'port' => $request->input('port'),
            'rcon' => $request->input('rcon'),
            'token' => (new Server)->generateSecurityToken(),
        ]);

        return redirect()
            ->route('admin.servers.index')
            ->with('status', 'success')
            ->with('message', "???????????? {$server->name} ?????????????? ????????????????!");
    }

    /**
     * Display the specified server.
     *
     * @param Server $server
     * @return Response
     */
//    public function show(Server $server)
//    {
//
//    }

    /**
     * Show the form for editing the specified server.
     *
     * @param Server $server
     * @return Application|Factory|View|Response
     */
    public function edit(Server $server) {
        return view('admin.servers.edit', compact('server'));
    }

    /**
     * Update the specified server in storage.
     *
     * @param  Request  $request
     * @param Server $server
     * @return RedirectResponse
     */
    public function update(Request $request, Server $server): RedirectResponse {
        $nameCheck = !empty($request->input('name')) && ($request->input('name') != $server->name);
        $ipCheck = !empty($request->input('ip')) && ($request->input('ip') != $server->ip);
        $portCheck = !empty($request->input('port')) && ($request->input('port') != $server->port);

        if ($nameCheck) {
            $rules['name'] = ['required', 'string', 'max:255', 'unique:servers'];
        }

        if ($ipCheck) {
            $rules['ip'] = ['required', 'ip'];
        }

        if ($portCheck) {
            $rules['port'] = ['required', 'integer', 'between:1,65535'];
        }


        if (isset($rules)) {
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        if ($nameCheck) {
            $server->name = strip_tags($request->input('name'));
        }

        if ($ipCheck) {
            $server->ip = $request->input('ip');
        }

        if ($portCheck) {
            $server->port = $request->input('port');
        }

        $server->rcon = $request->input('rcon');
        $server->save();

        return back()
            ->with('status', 'success')
            ->with('message', "???????????????????? ?? ?????????????? {$server->name} ?????????????? ??????????????????!");
    }

    /**
     * Remove the specified server from storage.
     *
     * @param Server $server
     * @return RedirectResponse
     */
    public function destroy(Server $server): RedirectResponse {
        $server->delete();

        return back()
            ->with('status', 'success')
            ->with('message', "???????????? {$server->name} ????????????!");
    }
}
