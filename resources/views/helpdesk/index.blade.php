<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Helpdesk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Frequently Asked Questions</h3>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">General</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I reset my password?</li>
                        <li>How do I update my profile information?</li>
                        <li>Where can I find company policies?</li>
                        <li>How do I contact HR?</li>
                        <li>What are the office hours?</li>
                        <li>How do I update my emergency contact information?</li>
                        <li>Where can I find the employee handbook?</li>
                        <li>How do I access the company directory?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Travel & Expenses</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I create a new travel request?</li>
                        <li>What information is required for a travel request?</li>
                        <li>How can I check the status of my travel request?</li>
                        <li>Can I modify a submitted travel request?</li>
                        <li>Who approves travel requests?</li>
                        <li>How do I submit expense reports?</li>
                        <li>What receipts do I need to keep?</li>
                        <li>What are the per diem rates?</li>
                        <li>How long does expense reimbursement take?</li>
                        <li>What expenses are covered by the company?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Leave Management</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I apply for leave?</li>
                        <li>How can I view my leave balance?</li>
                        <li>What is the process for leave approval?</li>
                        <li>Can I cancel a leave request?</li>
                        <li>How do I apply for sick leave?</li>
                        <li>What is the maternity/paternity leave policy?</li>
                        <li>How do I apply for emergency leave?</li>
                        <li>What documents are required for medical leave?</li>
                        <li>Can I carry forward unused leave?</li>
                        <li>How is leave calculated during public holidays?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Technical Support</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I report a technical issue?</li>
                        <li>Who do I contact for IT support?</li>
                        <li>How can I access the company VPN?</li>
                        <li>What should I do if I forget my login credentials?</li>
                        <li>How do I request new software?</li>
                        <li>How do I set up my work email on my phone?</li>
                        <li>What is the process for requesting new hardware?</li>
                        <li>How do I connect to office printers?</li>
                        <li>What is the password policy?</li>
                        <li>How do I access shared drives?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">HR & Payroll</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I view my payslip?</li>
                        <li>What should I do if my payslip is incorrect?</li>
                        <li>How can I update my bank details?</li>
                        <li>Who do I contact for payroll inquiries?</li>
                        <li>How do I apply for a salary advance?</li>
                        <li>When are salaries processed each month?</li>
                        <li>How do I update my tax information?</li>
                        <li>How can I view my employment contract?</li>
                        <li>What benefits am I entitled to?</li>
                        <li>How do I enroll in health insurance?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Security & Compliance</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I report a security incident?</li>
                        <li>What are the data protection policies?</li>
                        <li>Who is responsible for compliance?</li>
                        <li>How do I access compliance training?</li>
                        <li>What is the process for handling confidential information?</li>
                        <li>What are the clean desk policy requirements?</li>
                        <li>How do I report a potential policy violation?</li>
                        <li>What is the visitor policy?</li>
                        <li>How do I handle sensitive customer data?</li>
                        <li>What are the social media guidelines?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Office & Facilities</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I book a meeting room?</li>
                        <li>What are the parking facilities?</li>
                        <li>How do I get an access card?</li>
                        <li>What are the office maintenance procedures?</li>
                        <li>How do I report facility issues?</li>
                        <li>What are the cafeteria hours?</li>
                        <li>How do I request office supplies?</li>
                        <li>What is the seating arrangement policy?</li>
                        <li>How do I use the office gym?</li>
                        <li>What are the emergency evacuation procedures?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Training & Development</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>How do I access training materials?</li>
                        <li>What training programs are available?</li>
                        <li>How do I request additional training?</li>
                        <li>Where can I find learning resources?</li>
                        <li>How do I enroll in certification programs?</li>
                        <li>What is the training reimbursement policy?</li>
                        <li>How do I track my training progress?</li>
                        <li>Are there mandatory training requirements?</li>
                        <li>How do I access e-learning platforms?</li>
                        <li>What are the career development opportunities?</li>
                    </ul>
                </div>

                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Performance & Reviews</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>When are performance reviews conducted?</li>
                        <li>How do I set my performance goals?</li>
                        <li>What is the promotion process?</li>
                        <li>How are performance ratings determined?</li>
                        <li>How do I provide peer feedback?</li>
                        <li>What is the bonus structure?</li>
                        <li>How do I track my objectives?</li>
                        <li>What is the performance improvement process?</li>
                        <li>How do I request a performance review?</li>
                        <li>What are the key performance indicators?</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
