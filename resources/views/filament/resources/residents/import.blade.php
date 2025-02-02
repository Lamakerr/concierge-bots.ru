@extends('filament::layouts.app')

@section('content')
    <form action="{{ route('import.residents') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Импортировать Жильцов</button>
    </form>
@endsection