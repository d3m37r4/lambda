@extends('layouts.admin-layout')

@section('title', 'Управление ролями')

@section('admin.content')
    @include('admin.modals.confirm-delete')
    @include('admin.components.alert')
    <div class="card shadow-2 border">
        <div class="card-header">
            <div class="d-sm-flex justify-content-between">
                <div class="me-auto align-self-center">
                    <h5 class="card-title m-0">
                        <i class="fas fa-user-shield"></i>
                        {{ ('Управление ролями') }}
                    </h5>
                </div>
                <div class="d-grid">
                    @include('admin.components.link-add', ['route' => 'admin.roles.create', 'title' => 'Добавить'])
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="d-flex m-3">
                @include('admin.components.search-form')
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="col-2">{{ ('ID') }}</th>
                            <th class="col-3">{{ ('Роль') }}</th>
                            <th class="col-3">{{ ('Время создания') }}</th>
                            <th class="col-3">{{ ('Последнее обновление') }}</th>
                            <th style="min-width: 140px;">{{ ('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    {{ $role->id }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $role->name }}
                                    </span>
                                </td>
                                <td>
                                    {{ $role->created_at->format('d.m.Y - H:i:s') }}
                                </td>
                                <td>
                                    {{ $role->updated_at->format('d.m.Y - H:i:s') }}
                                </td>
                                <td>
                                    <a class="btn btn-info btn-floating btn-sm"
                                       data-mdb-toggle="tooltip"
                                       title="{{ ('Показать разрешения доступные для данной роли') }}"
                                       href="#">
                                        <i class="fas fa-info"></i>
                                    </a>
                                    <a class="btn btn-primary btn-floating btn-sm"
                                       data-mdb-toggle="tooltip"
                                       title="{{ ('Редактировать роль') }}"
                                       href="{{ route('admin.roles.edit', $role->id) }}">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <span class="d-inline-block" tabindex="0"
                                          data-mdb-toggle="tooltip" title="{{ ('Удалить роль') }}">
                                        <button class="btn btn-danger btn-floating btn-sm"
                                                type="button"
                                                data-mdb-toggle="modal"
                                                data-mdb-target="#confirmDelete"
                                                data-route="{{ route('admin.roles.destroy', $role->id) }}"
                                                data-rolename="{{ $role->name }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{ $roles->links() }}
@endsection

@push('secondary-scripts')
    <script>
        let modalDeleteRole = document.getElementById('confirmDelete');
        modalDeleteRole.addEventListener('show.bs.modal', function (event) {
            let confirmMsg = "{{ ('Вы действительно хотите удалить роль @rolename?') }}";
            let btn = event.relatedTarget;
            this.querySelector('.route').action = btn.getAttribute('data-route');

            let name = btn.getAttribute('data-rolename');
            confirmMsg = confirmMsg.replace('@rolename', name);

            this.querySelector('.modal-title').textContent = "{{ ('Удаление роли') }}";
            this.querySelector('.modal-msg').textContent = confirmMsg;
            this.querySelector('.modal-btn-title').textContent = "{{ ('Удалить роль') }}";
        });
    </script>
@endpush
