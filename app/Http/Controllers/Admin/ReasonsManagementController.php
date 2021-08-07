<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use App\Models\Server;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReasonsManagementController extends Controller {
    /**
     * Show the form for creating a new reason.
     *
     * @param Server $server
     * @return Application|Factory|View
     */
    public function create(Server $server) {
        $createdTime = Carbon::now()->format('d.m.Y - H:i:s');
        return view('admin.servers.reasons.create', compact('server', 'createdTime'));
    }

    /**
     * Store a newly created reason in storage.
     *
     * @param Request $request
     * @param Server $server
     * @return RedirectResponse
     */
    public function store(Request $request, Server $server): RedirectResponse {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255',
                Rule::unique('reasons')
                    ->where('server_id', $server->id)
            ],
            'months' => ['required', 'numeric', 'min:0'],
            'days' => ['required', 'numeric', 'min:0'],
            'hours' => ['required', 'numeric', 'min:0'],
            'minutes' => ['required', 'numeric', 'min:0'],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $time = CarbonInterval::months($request->input('months'))
            ->days($request->input('days'))
            ->hours($request->input('hours'))
            ->minutes($request->input('minutes'))
            ->totalMinutes;
        $reason = Reason::create([
            'title' => strip_tags($request->input('title')),
            'server_id' => $server->id,
            'time' => $time,
        ]);

        return redirect()
            ->route('admin.servers.show', $server->id)
            ->with('status', 'success')
            ->with('message', "Причина наказания {$reason->title} успешно добавлена!");
    }

    /**
     * Show the form for editing the specified reason.
     *
     * @param Server $server
     * @param Reason $reason
     * @return Application|Factory|View|Response
     */
    public function edit(Server $server, Reason $reason) {
        return view('admin.servers.reasons.edit', compact('server', 'reason'));
    }

    /**
     * Update the specified reason in storage.
     *
     * @param Request $request
     * @param Server $server
     * @param Reason $reason
     * @return RedirectResponse
     */
    public function update(Request $request, Server $server, Reason $reason): RedirectResponse {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255',
                Rule::unique('reasons')
                    ->where('server_id', $server->id)
                    ->whereNot('title', $reason->title)
            ],
            'months' => ['required', 'numeric', 'min:0'],
            'days' => ['required', 'numeric', 'min:0'],
            'hours' => ['required', 'numeric', 'min:0'],
            'minutes' => ['required', 'numeric', 'min:0'],
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $reason->title = strip_tags($request->input('title'));
        $reason->time = CarbonInterval::months($request->input('months'))
            ->days($request->input('days'))
            ->hours($request->input('hours'))
            ->minutes($request->input('minutes'))
            ->totalMinutes;
        $reason->save();

        return back()
            ->with('status', 'success')
            ->with('message', "Информация о причине наказания {$reason->title} успешно обновлена!");
    }

    /**
     * Remove the specified reason from storage.
     *
     * @param Server $server
     * @param Reason $reason
     * @return RedirectResponse
     */
    public function destroy(Server $server, Reason $reason): RedirectResponse {
        if (!$server) {
            return back()
                ->with('status', 'danger')
                ->with('message', 'Ошибка!');
        }

        $reason->delete();
        return back()
            ->with('status', 'success')
            ->with('message', "Причина {$reason->title} удалена!");
    }
}
