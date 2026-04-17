<?php
$destinataire = 'jijo94@live.fr';
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

$message = "
NOUVELLE RESERVATION - AMBULANCES MIRA 91
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
Via ambulancesmira91.fr
";

$headers = "From: noreply@ambulancesmira91.fr\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

mail($destinataire, $sujet, $message, $headers);

header('Content-Type: application/json');
echo json_encode(['success' => true]);
