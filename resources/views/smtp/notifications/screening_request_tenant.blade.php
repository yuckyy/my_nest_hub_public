@component('mail::message')
    <p>Dear {{ $firstName }} {{ $lastName }},</p>
    <p>As part of your application to reside at a property owned or managed by MYNESTHUB .com, you are required to
        complete a background questionnaire. You will also need to electronically sign a Disclosure and Authorization
        form ("Authorization Form") that will allow our background agency, TenantReports .com, to verify the information
        you provide and conduct a background check on you.</p>
    <p>The process takes approximately 15 minutes to complete and we recommend that you double check the information you
        provide to ensure there is no delay with your application for residency.</p>
    <p>An associate from TenantReports .com may contact you for additional information during the background check
        process. Please respond promptly so MYNESTHUB .com can complete the background check as quickly as possible so
        as not to delay the tenant selection process.</p>
    <p>Please click on the link below to begin the process. Please note this link will expire in 14 days, and if the
        background questionnaire is not completed within this time, you may not be considered for occupancy with
        MYNESTHUB .com. The link is good for one time use only. Once you have clicked on the link and submitted the
        background questionnaire, it will no longer be active.</p>
    <p>If you need any assistance while filling out the background questionnaire, you may contact MYNESTHUB .com at
        admin@MYNESTHUB.com or our background agency TenantReports .com at 610-622-0000 or by e-mail at
        info@tenantreports.com </p>
    <p>When you have completed the online background questionnaire form, please respond to MYNESTHUB .com at
        admin@MYNESTHUB.com to provide us with feedback or suggestions for improvement.</p>
    <p>Thank you,</p>
    <p>Please be careful to double check all the information you provide. Once you submit your information, you will not
        be able to modify or change it. Please note that TenantReports .com supports current versions of the most common
        browsers.</p>
    <p>&nbsp;</p>
    <p>Applicant Name: {{ $firstName }} {{ $lastName }}</p>
    <p>Application Link: <a href="{{ $quickappApplicantLink }}">Click here to begin your application</a></p>
    <p>&nbsp;</p>
    <p>NOTE – If you have any problems with the applicant link above, please copy and paste the following text into your
        browser's address field to begin the background questionnaire process:</p>
    <p>{{ $quickappApplicantLink }}</p>
    <p>&nbsp;</p>
    <p><em>This email is intended only for the person or entity to which it is addressed and may contain information
            that is privileged, confidential, or otherwise protected from disclosure. Dissemination, distribution, or
            copying of this e-mail or the information herein by anyone other than the intended recipient, or by an
            employee or agent responsible for delivering the message to the intended recipient, is prohibited. If you
            have received this e-mail in error, please notify us immediately by replying to the sender.</em></p>

@endcomponent
