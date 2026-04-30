<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Organization Chart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Current Employee -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your Information</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->employee_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Department</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->employee_department ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Designation</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->employee_designation ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Employee ID</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->employee_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manager Information -->
                @if($manager)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your Manager</h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $manager->employee_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Department</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $manager->department->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Designation</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $manager->designation->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Employee ID</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $manager->employee_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Team Members -->
                @if($teamMembers->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your Team Members</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($teamMembers as $member)
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $member->employee_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Designation</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $member->designation->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Subordinates -->
                @if($subordinates->isNotEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your Direct Reports</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($subordinates as $subordinate)
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $subordinate->employee_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Designation</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $subordinate->designation->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Department Colleagues -->
                @if($departmentColleagues->isNotEmpty())
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Department Colleagues</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($departmentColleagues as $colleague)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $colleague->employee_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Designation</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $colleague->designation->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
