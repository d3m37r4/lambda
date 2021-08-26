<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\URL;

class UsersManagementController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of users
     *
     * @return Application|Factory|View
     */
    public function index() {
        $users = User::paginate(env('PAGINATION_SIZE'));
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Application|Factory|View|Response
     */
    public function create() {
        $roles = Role::all();
        $createdTime = Carbon::now()->format('d.m.Y - H:i:s');
        $redirect = $this->getPreviousUrl(action([UsersManagementController::class, 'index']));

        return view('admin.users.create', compact('roles', 'redirect', 'createdTime'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse {
        // TODO: add choice of permissions
        $user = User::create($request->safe()->except('role'));
        $user->assignRole($request->safe()->only('role'));

        return redirect($request->input('redirect'))
            ->with('status', 'success')
            ->with('message', "Пользователь {$user->name} успешно создан!");
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @return Application|Factory|View|Response
     */
    public function show(User $user) {
        $permissions = $user->getAllPermissions();
        $redirect = $this->getPreviousUrl(action([UsersManagementController::class, 'index']));

        return view('admin.users.show', compact('user', 'permissions', 'redirect'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return Application|Factory|View|Response
     */
    public function edit(User $user) {
        $roles = Role::all();
        $redirect = $this->getPreviousUrl(action([UsersManagementController::class, 'index']));

        return view('admin.users.edit', compact('user', 'roles', 'redirect'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse {
        $user->update($request->safe()->except('role'));
        $user->syncRoles($request->safe()->only('role'));

        return back()
            ->with('status', 'success')
            ->with('message', "Информация о пользователе {$user->name} была успешно обновлена!");
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return Application|RedirectResponse|Response|Redirector|void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     */
    public function destroy(User $user) {
        $currentUser = Auth::user();

        if (is_null($currentUser)) {
            return abort(500);
        }

        if ($currentUser->id !== $user->id) {
            $user->save();
            $user->delete();

            return back()
                ->with('status', 'success')
                ->with('message', "Пользователь {$user->name} был удален!");
        }

        return back()
            ->with('status', 'danger')
            ->with('message', "Вы не можете удалить свой профиль!");
    }

    /**
     * Gets previous url or, if previous url is equal to current one, sets desired standard value
     *
     * @param string $defaultUrl
     * @return string
     */
    function getPreviousUrl(string $defaultUrl): string {
        $previous = URL::previous();
        return (($previous !== URL::current()) ? $previous : $defaultUrl);
    }
}
