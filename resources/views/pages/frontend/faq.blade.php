@extends('layouts.app')

@section('title', 'FAQ')

{{-- Optional CSS for plus/minus icon --}}
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
            <h2 class="fw-bold">Frequently Asked Questions</h2>
        </div>

        <div class="accordion" id="faqAccordion">
            @php
            $faqs = [
                ['q' => 'What is HeavenlyMatch.com?', 'a' => 'HeavenlyMatch.com is a Shariah-compliant Islamic matrimonial platform to help Muslims find pious life partners.'],
                ['q' => 'How does it work?', 'a' => 'Users create biodata according to Islamic guidelines, browse suitable matches, and can express interest or connect through the platform.'],
                ['q' => 'How much does it cost to submit biodata?', 'a' => 'Submitting biodata may be free or based on membership plans. Check the Pricing page for current fees.'],
                ['q' => 'Is this website open to everyone?', 'a' => 'The website is designed primarily for practicing Muslims seeking Shariah-compliant marriage matches.'],
                ['q' => 'Are there any special requirements for making biodata?', 'a' => 'Users must provide accurate Islamic-compliant personal and family details for verification and approval.'],
                ['q' => 'How confidential will my information be?', 'a' => 'All biodata information is strictly confidential and only shared with approved matches.'],
                ['q' => 'Why is my biodata not approved?', 'a' => 'Biodata may not be approved due to incomplete information or non-compliance with Islamic guidelines.'],
                ['q' => 'Can I submit my biodata again after rejection?', 'a' => 'Yes, you can correct the information and resubmit for approval.'],
                ['q' => 'Can I upload my biodata if I do not keep beard but pray regularly?', 'a' => 'Yes, keeping beard is recommended but not mandatory for biodata submission; Salah compliance is important.'],
                ['q' => 'Could I interact directly if a biodata is chosen by me?', 'a' => 'Direct interaction is allowed only after mutual interest and approval through the platform.'],
                ['q' => 'Can I submit without guardian consent?', 'a' => 'No, guardian consent is required for all minors.'],
                ['q' => 'Can I delete my biodata after marriage?', 'a' => 'Yes, users can delete their biodata anytime after marriage or for other reasons.'],
                ['q' => 'Are there post-marital service charges?', 'a' => 'No, there are no post-marital service charges.']
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="accordion-item mb-4 shadow rounded">
                <h2 class="accordion-header" id="faqHeading{{ $index }}">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="false" aria-controls="faqCollapse{{ $index }}">
                        <span>{{ $faq['q'] }}</span>
                        <span class="accordion-icon">+</span>
                    </button>
                </h2>
                <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ $faq['a'] }}
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