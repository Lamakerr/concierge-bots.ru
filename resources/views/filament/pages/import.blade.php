<x-filament-panels::page>
        <form action="{{ route('import.residents') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; flex-direction:column; gap:25px;">
                <label for="file">EXCEL</label>
                <input type="file" name="file" required>
                <button type="submit" style="color: black; background:white; padding:5px;">Импортировать Жильцов</button>
            </div>
        </form>
</x-filament-panels::page>
