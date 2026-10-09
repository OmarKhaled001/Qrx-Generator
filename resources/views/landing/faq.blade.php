{{-- FAQ: native disclosure widgets (keyboard and screen-reader friendly without JavaScript). Answers describe actual behaviour. --}}
@php
    $free = $plans->firstWhere('is_free', true) ?? \App\Models\Plan::free();
    $questions = [
        __('What is a dynamic QR code?') => __('The code itself opens a short link that you control. You can change what that link shows (a website, a menu, Wi-Fi details) at any time, and every printed copy follows.'),
        __('Can I change the content after printing?') => __('Yes. Edit the code in your dashboard and the change applies to every printed copy straight away. You never need to reprint.'),
        __('What is included in the free plan?') => __(':codes dynamic QR codes with :scans scans each, all 8 types and PNG and SVG downloads. No card needed.', ['codes' => Number::format($free->qr_limit), 'scans' => Number::format($free->scan_limit)]),
        __('What happens if I cancel my subscription?') => __('Your codes keep their links and you move to the free plan. A code that has passed the free plan’s scan limit shows a notice instead of your content until you upgrade again.'),
        __('What do you record when someone scans a code?') => __('The time, the type of device and the country. We do not store IP addresses: unique visitors are counted with an anonymous key that changes every day.'),
        __('Which file formats can I download?') => __('PNG and SVG on every plan, plus EPS for professional print on paid plans.'),
        __('Is the app available in Arabic?') => __('Yes. The whole app works in Arabic and English, with layouts that read naturally in both, and scan pages follow each visitor’s language.'),
    ];
@endphp
<section id="faq" class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:py-28">
    <x-landing.heading center :eyebrow="__('FAQ')" :title="__('Questions, answered')" />

    <div class="mt-12 divide-y divide-line rounded-2xl border border-line bg-surface shadow-card">
        @foreach ($questions as $question => $answer)
            <details class="group px-5 sm:px-6" @if ($loop->first) open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 py-5 font-semibold text-ink [&::-webkit-details-marker]:hidden">
                    {{ $question }}
                    <span class="grid size-7 shrink-0 place-items-center rounded-full bg-sunken text-ink-muted transition-transform duration-200 group-open:rotate-45" aria-hidden="true">
                        <x-heroicon-m-plus class="size-4" />
                    </span>
                </summary>
                <p class="-mt-1 pb-5 text-ink-muted">{{ $answer }}</p>
            </details>
        @endforeach
    </div>
</section>
