@extends('layouts.app')

@section('title', 'Guide')

<style>
/* Remove default Bootstrap arrow */
.accordion-button::after { display: none; }

/* Plus/Minus icon */
.accordion-button .accordion-icon {
    font-weight: bold;
    font-size: 1.25rem;
    margin-left: auto;
}

/* Shadow, rounded and spacing */
.accordion-item {
    transition: all 0.3s ease;
    padding: 0.5rem;
}
.accordion-button {
    padding: 1rem 1.25rem;
    cursor: pointer;
}
.accordion-button:hover {
    background-color: #f8f9fa;
}
</style>

@section('content')
    <section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">General Guidelines</h2>
        </div>

        <div class="accordion" id="guideAccordion">
            @php
            $guides = [
                ['q' => 'How to create an account in HeavenlyMatch.com?', 'a' => 'Go to the Register page, fill in the required details, and submit the form to create your account.'],
                ['q' => 'How to submit biodata in HeavenlyMatch.com?', 'a' => 'After logging in, navigate to the "Submit Biodata" section, fill in your personal and family information according to Islamic guidelines, and submit for verification.'],
                ['q' => 'How to edit biodata in HeavenlyMatch.com?', 'a' => 'Login to your account, go to "My Biodata", click edit, update your information, and save changes.'],
                ['q' => 'How to temporarily hide biodata in HeavenlyMatch.com?', 'a' => 'Login, go to "My Biodata" and select the option to temporarily hide your profile. Your biodata will not be visible until you reactivate it.'],
                ['q' => 'How to delete biodata in HeavenlyMatch.com?', 'a' => 'Login, go to "My Biodata", select delete, and confirm to permanently remove your biodata.'],
                ['q' => 'How will I complain about a biodata?', 'a' => 'Use the "Report" feature available on each biodata or contact our support team via the Help & Support section.'],
                ['q' => 'How to purchase connection?', 'a' => 'Go to the "Buy Connections" page, select the desired package, and complete payment via the available payment methods.'],
                ['q' => 'How will I get my “Connection” refund?', 'a' => 'Refunds are processed according to our refund policy. Contact support with your request, and it will be reviewed.'],
                ['q' => 'I want to delete my account from HeavenlyMatch.com. I don\'t need my remaining Connections. How can I get my refund?', 'a' => 'Contact our support team requesting account deletion and refund of unused Connections. The refund will be processed according to policy.']
            ];
            @endphp

            @foreach($guides as $index => $guide)
            <div class="accordion-item mb-4 shadow rounded">
                <h2 class="accordion-header" id="guideHeading{{ $index }}">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#guideCollapse{{ $index }}" aria-expanded="false" aria-controls="guideCollapse{{ $index }}">
                        <span>{{ $guide['q'] }}</span>
                        <span class="accordion-icon">+</span>
                    </button>
                </h2>
                <div id="guideCollapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="guideHeading{{ $index }}" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        {{ $guide['a'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


<script>
document.querySelectorAll('.accordion-button').forEach(button => {
    const collapseEl = document.querySelector(button.getAttribute('data-bs-target'));

    collapseEl.addEventListener('shown.bs.collapse', () => {
        button.querySelector('.accordion-icon').textContent = '−';
    });

    collapseEl.addEventListener('hidden.bs.collapse', () => {
        button.querySelector('.accordion-icon').textContent = '+';
    });
});
</script>
@endsection