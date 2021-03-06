@extends('layouts.admin-layout')

@section('title', 'Управление серверами')

@section('admin.content')
    @include('admin.modals.confirm-delete')
    @include('admin.components.alert')
    <div class="card mb-3">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="card-title">
                        <i class="bi bi-server"></i>
                        {{ ('Управление серверами') }}
                    </h5>
                </div>
                <div class="d-grid gap-2 d-md-block">
                    <a class="btn btn-success btn-sm"
                        href="{{ route('admin.servers.create') }}">
                        <i class="bi bi-shield-plus"></i>
                        {{ ('Добавить сервер') }}
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
                            <th class="col-5">{{ ('Сервер') }}</th>
                            <th style="width: 15%;">{{ ('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servers as $server)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    {{ $server->id }}
                                </td>
                                <td>
                                    {{ $server->name }}
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm"
                                       href="#">
                                        <i class="bi bi-info-square"></i>
                                    </a>
                                    <a class="btn btn-success btn-sm"
                                       href="{{ route('admin.servers.edit', $server->id) }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#confirmDelete"
                                            data-route="{{ route('admin.servers.destroy', $server->id) }}"
                                            data-servername="{{ $server->name }}">
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
    {{ $servers->links() }}
@endsection

@push('secondary-scripts')
    <script>
        let modalDeleteServer = document.getElementById('confirmDelete');
        modalDeleteServer.addEventListener('show.bs.modal', function (event) {
            let confirmMsg = "{{ ('Вы действительно хотите удалить сервер @servername?') }}";
            let btn = event.relatedTarget;
            this.querySelector('.route').action = btn.getAttribute('data-route');

            let name = btn.getAttribute('data-servername');
            confirmMsg = confirmMsg.replace('@servername', name);

            this.querySelector('.modal-title').textContent = "{{ ('Удаление сервера') }}";
            this.querySelector('.modal-msg').textContent = confirmMsg;
            this.querySelector('.modal-btn-title').textContent = "{{ ('Удалить сервер') }}";
        });
    </script>
@endpush

