<div class="custom-control custom-checkbox pt-2 ml-2">
    <input
            type="checkbox"
            class="custom-control-input"
            name="smoke"
            id="tenantCheck01"
            value="1"
            {{ $application->smoke ? 'checked' : '' }}
    >
    <label class="custom-control-label" for="tenantCheck01">Do you smoke?</label>
</div>

<div class="custom-control custom-checkbox pt-2 ml-2">
    <input
            type="checkbox"
            class="custom-control-input"
            name="evicted_or_unlawful"
            id="tenantCheck02"
            value="1"
            {{ $application->evicted_or_unlawful ? 'checked' : '' }}
    >
    <label class="custom-control-label" for="tenantCheck02">Have you ever been evicted from a rental or had an unlawful detainer judgement against you?</label>
</div>

<div class="custom-control custom-checkbox pt-2 ml-2">
    <input
            type="checkbox"
            class="custom-control-input"
            name="felony_or_misdemeanor"
            id="tenantCheck03"
            value="1"
            {{ $application->felony_or_misdemeanor ? 'checked' : '' }}
    >
    <label class="custom-control-label" for="tenantCheck03">Have you ever been convicted of a felony or misdemeanor (other than a traffic or parking violation)?</label>
</div>

<div class="custom-control custom-checkbox pt-2 ml-2">
    <input
            type="checkbox"
            class="custom-control-input"
            name="refuse_to_pay_rent"
            id="tenantCheck04"
            value="1"
            {{ $application->refuse_to_pay_rent ? 'checked' : '' }}
    >
    <label class="custom-control-label" for="tenantCheck04">Have you ever refused to pay rent when it was due?</label>
</div>

<input type="hidden" name="additionalInfo" value="1">
