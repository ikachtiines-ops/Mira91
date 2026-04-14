<?php
// ============================================================
//  Ambulances Mira 91 — Traitement formulaire de réservation
//  Envoie un email à jijo94@live.fr à chaque réservation
// ============================================================

$destinataire = 'jijo94@live.fr';
$sujet        = '🚑 Nouvelle réservation — Ambulances Mira 91';

// Sécuriser les données reçues
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val ?? '')));
}

// Récupération des champs
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

// Validation champs obligatoires
if (!$nom || !$tel || !$depart || !$destination || !$date || !$heure || !$transport) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants.']);
    exit;
}

// Construction du message email
$message = "
=======================================================
  NOUVELLE RÉSERVATION — AMBULANCES MIRA 91
=======================================================

👤 PATIENT
  Nom          : $nom
  Téléphone    : $tel
  Date naiss.  : $ddn

🚑 TYPE DE TRANSPORT
  Type         : " . strtoupper($transport) . "

📍 TRAJET
  Départ       : $depart
  Destination  : $destination
  Date         : $date
  Heure        : $heure

🏠 ACCESSIBILITÉ
  Étage        : $etage
  Ascenseur    : $ascenseur
  Autonomie    : $autonomie
  Fauteuil     : $fauteuil

💬 COMMENTAIRE
$commentaire

=======================================================
  Reçu le : " . date('d/m/Y à H:i') . "
  Via ambulancesmira91.fr
=======================================================
";

// En-têtes email
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: contact@ambulancesmira91.fr\r\n";
$headers .= "Reply-To: $tel\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Envoi
$envoye = mail($destinataire, $sujet, $message, $headers);

// Réponse JSON pour le JS
header('Content-Type: application/json');
if ($envoye) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi.']);
}
