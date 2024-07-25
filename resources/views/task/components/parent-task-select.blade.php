@foreach ($tasks as $task)
    <option value="{{ $task->id }}" {{ $selectedValue == $task->id ? 'selected' : '' }}>
        @foreach ($task->parents as $parent)
            {{ $parent->name . ' > ' }}
        @endforeach
        {{ $task->name }}
    </option>
@endforeach
