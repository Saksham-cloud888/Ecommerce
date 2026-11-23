<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New merchant registration</title>
</head>
<body style="margin: 0; padding: 24px; background: #f3f4f6; color: #363636; font-family: Arial, sans-serif;">
    <main style="max-width: 600px; margin: 0 auto; padding: 32px; border-radius: 12px; background: #ffffff;">
        <h1 style="margin-top: 0; color: #f85f11;">New merchant registration</h1>
        <p>A merchant application has been submitted and is waiting for review.</p>

        <h2 style="font-size: 18px;">Application details</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Owner</td>
                <td style="padding: 8px 0;">{{ $merchant->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Shop</td>
                <td style="padding: 8px 0;">{{ $merchant->shop_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Email</td>
                <td style="padding: 8px 0;">{{ $merchant->email }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Contact</td>
                <td style="padding: 8px 0;">{{ $merchant->contact }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Status</td>
                <td style="padding: 8px 0;">{{ ucfirst($merchant->status) }}</td>
            </tr>
        </table>

        <p style="margin: 28px 0;">
            <a href="{{ $reviewUrl }}" style="display: inline-block; padding: 12px 20px; border-radius: 8px; background: #f85f11; color: #ffffff; text-decoration: none;">Review in admin panel</a>
        </p>

        <p style="margin-bottom: 0; color: #6b7280; font-size: 13px;">This is an automated notification from {{ config('app.name', 'Ecommerce') }}.</p>
    </main>
</body>
</html>
