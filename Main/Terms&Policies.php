<?php
include('NavigationBar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Policies</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Container for terms and policies */
        .terms-policies {
            padding: 80px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            text-align: center;
        }

        .terms-policies h2 {
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            color: #333;
        }

        /* Card styles for Terms of Service and Privacy Policy */
        .card {
            flex: 1;
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 400px;
            transition: transform 0.3s ease;
            text-align: center;
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #333;
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        .card:hover {
            transform: scale(1.05);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .terms-policies {
                flex-direction: column;
                padding: 20px;
            }

            .card {
                margin-bottom: 20px;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Terms and Policies Section -->
    <div class="terms-policies">
        <h2>TERMS AND POLICIES</h2>

        <!-- Terms of Service Card -->
        <div class="card">
            <h3>Terms of Service</h3>
            <p>
                Our Terms of Service govern the use of our platform and services. By using our services, you agree to comply with these terms.
            </p>
            <p>
                We reserve the right to modify these terms at any time. Please review them regularly for updates.
            </p>
        </div>

        <!-- Privacy Policy Card -->
        <div class="card">
            <h3>Privacy Policy</h3>
            <p>
                Our Privacy Policy explains how we collect, use, and protect your personal information. We are committed to safeguarding your privacy.
            </p>
            <p>
                We ensure your data is handled securely and responsibly. For more details, please review our full privacy policy.
            </p>
        </div>
    </div>

</body>
</html>