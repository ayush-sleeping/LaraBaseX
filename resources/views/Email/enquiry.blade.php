@component('mail::message')
Hi {{ $first_name }} {{ $last_name }},<br /><br />
Thank you for your enquiry on LaraBaseX.<br />
We have received your enquiry with the following details:<br /><br />

First Name: {{ $first_name }}<br />
Last Name: {{ $last_name }}<br />
Email: {{ $email }}<br />
Subject: {{ $subject }}<br />
Description: {{ $description }}<br /><br />

Thanks,<br>
Support Team,<br />
LaraBaseX<br />
@endcomponent
