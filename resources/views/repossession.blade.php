<!DOCTYPE html>
<html>
<head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    h1 {
        background-color: #0077b6;
        color: white;
        padding: 20px;
        margin: 0;
    }

    p {
        margin: 10px 0;
        padding: 0 20px;
    }

    .content {
        padding: 20px;
        background-color: #ffffff;
        margin: 20px;
        border-radius: 5px;
    }

    .footer {
        background-color: #0077b6;
        color: white;
        text-align: center;
        padding: 10px;
    }

    .signature {
        margin-top: 20px;
        text-align: right;
        font-style: italic;
        color: #444;
    }
</style>
</head>
<body>

<h1>{{ $mailData['title']}}</h1>

<div class="content">
    <p>{{ $mailData['body'] }}</p>

    <p>Dear valued customer,</p>
    <p>We hope this message finds you well. We would like to bring to your attention that your payment for the bike loan is currently overdue. Unfortunately, we have not yet received your payment as of the scheduled due date.</p>
    <p>It is of utmost importance that you make the payment promptly to avoid any inconvenience. If you have any questions or concerns regarding your payment, please do not hesitate to reach out to our dedicated customer support team.</p>
    <p>Thank you for choosing Bisikleta Bike Shop. Your business is highly appreciated, and we look forward to your swift resolution of the payment matter.</p>

    <div class="signature">
        Sincerely,<br>
        The Bisikleta Bike Shop Team
    </div>
</div>

<div class="footer">
    &copy; {{ date('Y') }} Bisikleta Bike Shop
</div>

</body>
</html>
