@extends('layouts.admin-layout')

@section('title', 'Управление ролями')

@section('admin.content')
    @include('admin.modals.confirm-delete')
    @include('admin.components.alert')
    <div class="card mb-3">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="card-title">
                        <i class="bi bi-shield-fill"></i>
                        {{ ('Управление ролями') }}
                    </h5>
                </div>
                <div class="d-grid gap-2 d-md-block">
                    <a class="btn btn-success btn-sm"
                        href="{{ route('admin.roles.create') }}">
                        <i class="bi bi-shield-plus"></i>
                        {{ ('Создать роль') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="col-1">{{ ('#') }}</th>
                            <th class="col-1">{{ ('ID') }}</th>
                            <th class="col-5">{{ ('Роль') }}</th>
                            <th style="width: 15%;">{{ ('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    {{ $role->id }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $role->name }}
                                    </span>
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm"
                                       href="#">
                                        <i class="bi bi-info-square"></i>
                                    </a>
                                    <a class="btn btn-success btn-sm"
                                       href="{{ route('admin.roles.edit', $role->id) }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#confirmDelete"
                                            data-route="{{ route('admin.roles.destroy', $role->id) }}"
                                            data-rolename="{{ $role->name }}">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
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

