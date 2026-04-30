@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Full & Final Settlement Calculator</h2>

        <!-- Employee Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold mb-3">Employee Details</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-600">Employee Name</p>
                    <p class="font-medium">{{ $resignation->employee->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Employee ID</p>
                    <p class="font-medium">{{ $resignation->employee->employee_id }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Last Working Day</p>
                </div>
            </div>
        </div>

        <!-- Calculator Form -->
        <form id="fnfCalculator" class="space-y-6">
            @csrf

            <!-- Salary Components -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                    <input type="number" name="basic_salary" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Days Worked in Current Month</label>
                    <input type="number" name="days_worked" required min="0" max="31"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Unused Leaves</label>
                    <input type="number" name="unused_leaves" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Years of Service</label>
                    <input type="number" name="years_of_service" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Additional Components -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bonus</label>
                    <input type="number" name="bonus"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Incentives</label>
                    <input type="number" name="incentives"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notice Period Served</label>
                    <select name="notice_period_served"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <!-- Deductions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tax Deduction</label>
                    <input type="number" name="tax_deduction"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Loan Balance</label>
                    <input type="number" name="loan_balance"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Other Deductions</label>
                    <input type="number" name="other_deductions"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="calculateFnF()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Calculate
                </button>

                <button type="submit" form="generateFnF"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Generate F&F
                </button>
            </div>
        </form>

        <!-- Results Section -->
        <div id="calculationResults" class="mt-8 hidden">
            <h3 class="text-lg font-semibold mb-4">Calculation Results</h3>
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-700">Earnings</h4>
                        <dl class="mt-2 space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Proportionate Salary</dt>
                                <dd class="font-medium" id="proportionate_salary">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Leave Encashment</dt>
                                <dd class="font-medium" id="leave_encashment">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Gratuity</dt>
                                <dd class="font-medium" id="gratuity">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Bonus</dt>
                                <dd class="font-medium" id="bonus">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Incentives</dt>
                                <dd class="font-medium" id="incentives">-</dd>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <dt class="font-medium">Total Earnings</dt>
                                <dd class="font-medium text-green-600" id="total_earnings">-</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-700">Deductions</h4>
                        <dl class="mt-2 space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tax Deduction</dt>
                                <dd class="font-medium" id="tax_deduction">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Loan Balance</dt>
                                <dd class="font-medium" id="loan_balance">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Notice Period Recovery</dt>
                                <dd class="font-medium" id="notice_recovery">-</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Other Deductions</dt>
                                <dd class="font-medium" id="other_deductions">-</dd>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <dt class="font-medium">Total Deductions</dt>
                                <dd class="font-medium text-red-600" id="total_deductions">-</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Net Payable -->
                <div class="mt-6 border-t pt-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-semibold">Net Payable Amount</h4>
                        <span class="text-xl font-bold text-blue-600" id="net_payable">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generate F&F Form -->
        <form id="generateFnF" action="{{ route('fnf.generate', $resignation->id) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="calculation_data" id="calculation_data">
        </form>
    </div>
</div>

<!-- Status Messages -->
@if (session('success'))
<div class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

@if (session('error'))
<div class="fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
    <p class="font-medium">{{ session('error') }}</p>
</div>
@endif

<script>
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }
</script>

<script>
    function calculateFnF() {
        const form = document.getElementById('fnfCalculator');
        const formData = new FormData(form);

        // Convert FormData to object
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value === '' ? 0 : Number(value);
        });

        // Make API call to calculate
        fetch("{{ route('fnf.calculate', $resignation->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                // Show results section
                document.getElementById('calculationResults').classList.remove('hidden');

                // Add input data to result object
                result.basic_salary = data.basic_salary;
                result.days_worked = data.days_worked;
                result.unused_leaves = data.unused_leaves;
                result.years_of_service = data.years_of_service;
                result.notice_period_served = data.notice_period_served;

                // Update all result fields
                const fields = [
                    'proportionate_salary',
                    'leave_encashment',
                    'gratuity',
                    'bonus',
                    'incentives',
                    'total_earnings',
                    'tax_deduction',
                    'loan_balance',
                    'notice_recovery',
                    'other_deductions',
                    'total_deductions',
                    'net_payable'
                ];

                fields.forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        element.textContent = formatCurrency(result[field]);
                    }
                });

                // Store complete calculation data for form submission
                document.getElementById('calculation_data').value = JSON.stringify(result);

                // Show the generate button
                document.getElementById('generateFnF').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to calculate F&F. Please try again.');
            });
    }

    // Add event listener for form submission
    document.getElementById('generateFnF').addEventListener('submit', function(e) {
        const calculationData = document.getElementById('calculation_data').value;
        if (!calculationData) {
            e.preventDefault();
            alert('Please calculate F&F before generating the settlement.');
            return;
        }

        // Parse the calculation data to verify all required fields are present
        try {
            const data = JSON.parse(calculationData);
            const requiredFields = [
                'basic_salary',
                'days_worked',
                'unused_leaves',
                'years_of_service'
            ];

            const missingFields = requiredFields.filter(field => !data[field]);

            if (missingFields.length > 0) {
                e.preventDefault();
                alert('Missing required fields: ' + missingFields.join(', '));
                return;
            }
        } catch (error) {
            e.preventDefault();
            alert('Invalid calculation data. Please recalculate.');
            return;
        }
    });
</script>

@endsection