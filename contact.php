<?php
// Configuration SMTP OVH
$smtp_host = 'ssl0.ovh.net';
$smtp_port = 465;
$smtp_user = 'contact@ambulancesmira91.fr';
$smtp_pass = 'B1sm!lah94';
$destinataire = 'contact@ambulancesmira91.fr';
$sujet = 'Nouvelle reservation - Ambulances Mira 91';

function clean($val) {
    return htmlspecialchars(strip_tags(trim($val ?? '')));
}

$nom         = clean($_POST['nom'] ?? '');
$tel         = clean($_POST['tel'] ?? '');
$ddn         = clean($_POST['ddn'] ?? '');
$transport   = clean($_POST['transport'] ?? '');
$depart      = clean($_POST['depart'] ?? '');
$destination = clean($_POST['destination'] ?? '');
$date        = clean($_POST['date'] ?? '');
$heure       = clean($_POST['heure'] ?? '');
$etage       = clean($_POST['etage'] ?? '');
$ascenseur   = clean($_POST['ascenseur'] ?? '');
$autonomie   = clean($_POST['autonomie'] ?? '');
$fauteuil    = clean($_POST['fauteuil'] ?? '');
$commentaire = clean($_POST['commentaire'] ?? '');

$message = "NOUVELLE RESERVATION - AMBULANCES MIRA 91
==========================================

PATIENT
  Nom          : $nom
  Telephone    : $tel
  Date naiss.  : $ddn

TRANSPORT
  Type         : " . strtoupper($transport) . "

TRAJET
  Depart       : $depart
  Destination  : $destination
  Date         : $date
  Heure        : $heure

ACCESSIBILITE
  Etage        : $etage
  Ascenseur    : $ascenseur
  Autonomie    : $autonomie
  Fauteuil     : $fauteuil

COMMENTAIRE
$commentaire

==========================================
Recu le : " . date('d/m/Y a H:i') . "
Via ambulancesmira91.fr";

$boundary = md5(time());

// Construire le corps avec pièces jointes
$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$body .= $message . "\r\n";

foreach (['vitale' => 'carte_vitale', 'mutuelle' => 'carte_mutuelle'] as $key => $label) {
    if (!empty($_FILES[$key]['tmp_name']) && $_FILES[$key]['error'] === 0) {
        $filename = $label . '_' . basename($_FILES[$key]['name']);
        $filedata = chunk_split(base64_encode(file_get_contents($_FILES[$key]['tmp_name'])));
        $filetype = $_FILES[$key]['type'];
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $filetype; name=\"$filename\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
        $body .= $filedata . "\r\n";
    }
}
$body .= "--$boundary--";

// Connexion SMTP manuelle
$socket = fsockopen('ssl://' . $smtp_host, $smtp_port, $errno, $errstr, 30);

if ($socket) {
    fgets($socket, 512);
    fputs($socket, "EHLO ambulancesmira91.fr\r\n");
    while ($line = fgets($socket, 512)) { if (substr($line, 3, 1) == ' ') break; }
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($smtp_user) . "\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($smtp_pass) . "\r\n");
    fgets($socket, 512);
    fputs($socket, "MAIL FROM: <" . $smtp_user . ">\r\n");
    fgets($socket, 512);
    fputs($socket, "RCPT TO: <" . $destinataire . ">\r\n");
    fgets($socket, 512);
    fputs($socket, "DATA\r\n");
    fgets($socket, 512);
    fputs($socket, "From: Ambulances Mira 91 <" . $smtp_user . ">\r\n");
    fputs($socket, "To: " . $destinataire . "\r\n");
    fputs($socket, "Subject: " . $sujet . "\r\n");
    fputs($socket, "MIME-Version: 1.0\r\n");
    fputs($socket, "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n");
    fputs($socket, "\r\n");
    fputs($socket, $body . "\r\n");
    fputs($socket, ".\r\n");
    fgets($socket, 512);
    fputs($socket, "QUIT\r\n");
    fclose($socket);
}

header('Location: merci.html');
exit;
