<?php
// check param
$domain = $argv[1] ?? ($_GET['domain'] ?? null);
if (!$domain) {
    print 'Please input your domain target!';
    return;
}

require_once __DIR__ . '/../vendor/autoload.php';

// load whois factory
use Iodev\Whois\Factory;

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// Creating default configured client
$whois = Factory::get()->createWhois();

// Checking availability
if ($whois->isDomainAvailable($domain)) {
    print "Bingo! Domain '" . $domain . "' is available! :)";

    /* send mail notification */
    echo "<br>";
    sendMail('youremail@for.notif', $domain);
} else {
    print "Domain '" . $domain . "' is not available";
}

function sendMail($to, $domain)
{
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.host'; // Set the SMTP server to send through
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'username'; // SMTP username
        $mail->Password = 'password'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port = 587; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('emailfrom@your.domain', 'Email Name');
        $mail->addAddress($to); // Add a recipient
        // $mail->addAddress('ellen@example.com'); // Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Domain "' . $domain . '" is Available!';
        $mail->Body = 'You can buy it <b>NOW</b>!';

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
