@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px; font-weight: 600; color: #121A26;">
            New TVD Refund Application
        </span>

        <p style="font-size: 16px; color: #5E5E5E; line-height: 24px;">
            A new TVD refund application has been submitted. Please review the details below.
        </p>
    </div>

    <div style="background: #f8f8f8; border-radius: 6px; padding: 20px 24px; margin-bottom: 32px;">
        <div style="font-size: 18px; font-weight: 600; color: #121A26; margin-bottom: 16px;">
            Submission Details
        </div>

        <table style="width: 100%; font-size: 15px; color: #384860; line-height: 28px;">
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Renter Full Chinese Name</td>
                <td>{{ $submission->chinese_name }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Renter Full English Name</td>
                <td>{{ $submission->english_name }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Last Rental Model</td>
                <td>{{ $submission->rental_model }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Last Rental Return Date</td>
                <td>{{ $submission->return_date }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Renter's Contact Number</td>
                <td>{{ $submission->contact_number }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Renter's Email Address</td>
                <td>{{ $submission->email }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Refund Type</td>
                <td>{{ ucfirst($submission->refund_type) }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">FPS / Local Bank Info</td>
                <td>{{ $submission->local_bank_info ?: '—' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Overseas Bank / SWIFT Info</td>
                <td>{{ $submission->overseas_bank_info ?: '—' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; padding-right: 16px; white-space: nowrap;">Submitted At</td>
                <td>{{ $submission->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
    </div>
@endcomponent
