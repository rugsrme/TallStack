<div x-data="{ expanded: false }">
    @if (session()->has('message'))
        <div class="rounded-lg bg-emerald-500 p-3">
            {{ session('message') }}

            <div class="shrink-0 sm:ml-3">
                <button type="button" class="-mr-1 flex rounded-md p-2 transition focus:outline-none sm:-mr-2"
                    :class="{
                        'hover:bg-indigo-600 focus:bg-indigo-600': style ==
                            'success',
                        'hover:bg-red-600 focus:bg-red-600': style == 'danger'
                    }"
                    aria-label="Dismiss" x-on:click="show = false">
                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
    <x-slot name="header">
        <div class="mb-3 bg-slate-300 py-2 text-center dark:bg-slate-600">
            <span>For tips on naming workorders please see this </span><span><a href="/dashboard"
                    class="border-b">FAQ</a></span>
            <br />
            <span><a class="border-b" href="/workorders"><i class="fa fa-grip-vertical"></i>
                    Work Order
                    Cards
                </a></span>
        </div>
    </x-slot>
    <div class="mb-3 w-full items-center justify-between px-3 sm:flex">
        <div class="">
            <x-jet-button class="m-auto w-full border border-[#002a7a] bg-blue-900 py-3" wire:click="confirmOpenCreate">
                Submit a Work Order Request
            </x-jet-button>
        </div>
        <div>

            <x-jet-dialog-modal wire:model='confirmingOpenCreate' class="z-50">
                <x-slot name="title">
                </x-slot>
                <x-slot name="content">
                    @if ($workorder !== null)
                        <x-workorder-create-modal :locations="$locations" :workorder="$workorder" :status="$status"
                            :priority="$priority" :assigned_to="$assigned_to" :user_id="$workorder->user_id" />
                    @endif
                </x-slot>
            </x-jet-dialog-modal>
        </div>
        <div class="">
            <x-jet-input class="w-full" type="search" wire:change='$refresh' placeholder="Search Work Orders"
                wire:model.debounce='search' />
        </div>
        <div class="">
            <x-jet-label for="filterType">
                <select name='filter'
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-300 focus:ring focus:ring-slate-200 focus:ring-opacity-50"
                    wire:model='filterType'>
                    <option value="">All Work Orders</option>
                    <option value="open">Open</option>
                    <option value="request">Request</option>
                    <option value="closed">Closed</option>
                </select>
            </x-jet-label>
        </div>
    </div>
    <div>
        <table
            class="w-screen border-collapse border border-slate-700 bg-slate-200 text-sm dark:border-slate-200 dark:bg-slate-800 dark:text-slate-400">
            <thead>
                <tr>
                    <th class="border border-slate-800 dark:border-slate-200">ID</th>
                    <th class="border border-slate-800 dark:border-slate-200">Task</th>
                    <th class="border border-slate-800 dark:border-slate-200">Description</th>
                    <th class="border border-slate-800 dark:border-slate-200">Status</th>
                    <th class="border border-slate-800 dark:border-slate-200">Priority</th>
                    <th class="border border-slate-800 dark:border-slate-200">Assigned</th>

                    <th class="border border-slate-800 dark:border-slate-200">Created</th>
                    <th class="border border-slate-800 dark:border-slate-200">Updated</th>
                    @if (Auth::user()->is_admin)
                        <th class="border border-slate-800 dark:border-slate-200">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($workorders as $workorder)
                    <div>

                        <tr @click="expanded = ! expanded"
                            class="odd:bg-slate-100 even:bg-slate-200 dark:odd:bg-slate-900 dark:even:bg-slate-600"
                            wire:key='{{ $workorder->id }}'>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">{{ $workorder->id }}</td>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">{{ $workorder->task }}</td>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">{{ $workorder->desc }}</td>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">{{ $workorder->status }}
                            </td>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">{{ $workorder->priority }}
                            </td>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">
                                {{ $workorder->assigned_to }}
                            <td class="border border-slate-800 p-1 dark:border-slate-200">
                                {{ $workorder->created_at->diffForHumans() }}</td>
                            <td class="border border-slate-800 p-1 dark:border-slate-200">
                                {{ $workorder->updated_at->diffForHumans() }}
                            </td>
                            @if (Auth::user()->is_admin)
                                <td class="flex border-t border-slate-800 p-1 dark:border-slate-200">
                                    <button aria-label="edit work order"
                                        wire:click='confirmOpenCreate({{ $workorder->id }})'
                                        class="w-full rounded-lg bg-orange-600 text-white">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <div class="mx-1 w-full" x-data>
                                        <button aria-lable='delete workorder'
                                            class="w-full rounded-lg bg-red-600 text-white"
                                            @click="confirm('Are you sure you want to delete this work order?',$wire.deleteWorkorder({{ $workorder->id }}))">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                </td>
                            @endif
                        </tr>
                        <tr x-show="expanded" x-collapse style="display: none">
                            <td class="border border-slate-800 p-1 dark:border-slate-200">{{ $workorder->id }}
                            <x-comment :workorder="$workorder"/>
                            <x-comments :workorder="$workorder" />
                            </td>
                        </tr>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
