<?php
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
$headers  = "From: noreply@ambulancesmira91.fr\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
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

mail($destinataire, $sujet, $body, $headers);

// Rediriger vers page de confirmation
header('Location: merci.html');
exit;
