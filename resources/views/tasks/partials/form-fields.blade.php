<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input
        id="title"
        class="block mt-1 w-full"
        type="text"
        name="title"
        :value="old('title', $task->title ?? '')"
        required
        autofocus
    />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea
        id="description"
        name="description"
        rows="5"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required
    >{{ old('description', $task->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="assigned_to" :value="__('Assign To')" />
    <select
        id="assigned_to"
        name="assigned_to"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required
    >
        <option value="">{{ __('Select an employee') }}</option>
        @foreach ($employees as $employee)
            <option
                value="{{ $employee->id }}"
                @selected((string) old('assigned_to', $task->assigned_to ?? '') === (string) $employee->id)
            >
                {{ $employee->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="status" :value="__('Status')" />
    <select
        id="status"
        name="status"
        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required
    >
        <option value="pending" @selected(old('status', $task->status ?? 'pending') === 'pending')>{{ __('Pending') }}</option>
        <option value="done" @selected(old('status', $task->status ?? '') === 'done')>{{ __('Done') }}</option>
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>
