<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support Ticket</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #f8f9fb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h2 style="color: #2563eb; margin-top: 0;">New Support Ticket</h2>
        
        <p><strong>From:</strong> {{ $ticketData['name'] }} ({{ $ticketData['email'] }})</p>
        <p><strong>Category:</strong> {{ $ticketData['category'] }}</p>
        <p><strong>Subject:</strong> {{ $ticketData['subject'] }}</p>
        
        <hr style="border: 0; border-top: 1px solid #d1d5db; margin: 20px 0;">
        
        <h3 style="margin-top: 0;">Message:</h3>
        <p style="white-space: pre-wrap; background: #fff; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">{{ $ticketData['message'] }}</p>
    </div>

    <p style="font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px;">
        This email was sent from the CivicSeva Help & Support page.
    </p>

</body>
</html>
