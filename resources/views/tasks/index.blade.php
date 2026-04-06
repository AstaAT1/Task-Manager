<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Tasks') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    @manager
                        {{ __('Manage and assign work for your team.') }}


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
</div>




                    @else
                        {{ __('Track and complete the work assigned to you.') }}
                    @endmanager
                </p>
            </div>

            @manager
                <a
                    href="{{ route('tasks.create') }}"
                    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    {{ __('Create Task') }}
                </a>
            @endmanager
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($tasks->isEmpty())
                        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('No tasks yet') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                @manager
                                    {{ __('Create the first task to start assigning work to employees.') }}
                                @else
                                    {{ __('Tasks assigned to you will appear here.') }}
                                @endmanager
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Title') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Assigned To') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Created By') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($tasks as $task)
                                        <tr>
                                            <td class="px-4 py-4 align-top">
                                                <div class="font-medium text-gray-900">{{ $task->title }}</div>
                                                <div class="mt-1 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</div>
                                            </td>
                                            <td class="px-4 py-4 align-top text-sm text-gray-700">
                                                {{ $task->assignedTo->name }}
                                            </td>
                                            <td class="px-4 py-4 align-top text-sm text-gray-700">
                                                {{ $task->creator->name }}
                                            </td>
                                            <td class="px-4 py-4 align-top">
                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $task->status === 'done' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ ucfirst($task->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 align-top">
                                                <div class="flex items-center justify-end gap-2">
                                                    @employee
                                                        @can('mark-task-done', $task)
                                                            @if ($task->status !== 'done')
                                                                <form method="POST" action="{{ route('tasks.done', $task) }}">
                                                                    @csrf
                                                                    @method('PATCH')

                                                                    <button type="submit" class="text-sm font-medium text-green-600 hover:text-green-800">
                                                                        {{ __('Mark Done') }}
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="text-sm text-gray-400">{{ __('Completed') }}</span>
                                                            @endif
                                                        @endcan
                                                    @else
                                                        <span class="text-sm text-gray-400">{{ __('No actions') }}</span>
                                                    @endemployee
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
