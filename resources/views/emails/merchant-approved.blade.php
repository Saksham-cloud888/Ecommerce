<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Merchant account approved</title>
</head>
<body style="margin: 0; padding: 24px; background: #f3f4f6; color: #363636; font-family: Arial, sans-serif;">
    <main style="max-width: 600px; margin: 0 auto; padding: 32px; border-radius: 12px; background: #ffffff;">
        <h1 style="margin-top: 0; color: #04a53f;">Your shop has been approved!</h1>
        <p>Hello {{ $merchant->name }},</p>
        <p>Your merchant application for <strong>{{ $merchant->shop_name }}</strong> has been approved. You can now sign in to your merchant dashboard.</p>

        <h2 style="font-size: 18px;">Sign-in details</h2>
        <p><strong>Email:</strong> {{ $merchant->email }}</p>
        <p><strong>Temporary password:</strong> {{ $temporaryPassword }}</p>
        <p style="color: #9a3412;"><strong>Security:</strong> Sign in and change this temporary password as soon as possible. Do not share it with anyone.</p>

        <p style="margin: 28px 0;">
            <a href="{{ $loginUrl }}" style="display: inline-block; padding: 12px 20px; border-radius: 8px; background: #04a53f; color: #ffffff; text-decoration: none;">Open merchant login</a>
        </p>

        <p style="margin-bottom: 0; color: #6b7280; font-size: 13px;">This is an automated message from {{ config('app.name', 'Ecommerce') }}.</p>
    </main>
</body>
</html>
