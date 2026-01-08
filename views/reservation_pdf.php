<?php
declare(strict_types=1);

use utils\Guard;
use services\ReservationService;
use services\PdfService;

Guard::requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $data = (new ReservationService())->receipt($id);

    $html = '
    <div style="font-family: DejaVu Sans, sans-serif;">
      <h2 style="margin:0;">Reçu de réservation</h2>
      <p style="margin:6px 0;">Réservation #'.(int)$data['id'].' — <b>'.htmlspecialchars($data['status']).'</b></p>
      <hr>

      <h3 style="margin-bottom:6px;">Voyageur</h3>
      <p style="margin:0;">'.htmlspecialchars($data['traveler_name']).' ('.htmlspecialchars($data['traveler_email']).')</p>

      <h3 style="margin:14px 0 6px;">Logement</h3>
      <p style="margin:0;"><b>'.htmlspecialchars($data['rental_title']).'</b></p>
      <p style="margin:0;">Ville: '.htmlspecialchars($data['rental_city']).'</p>
      <p style="margin:0;">Adresse: '.htmlspecialchars((string)$data['rental_address']).'</p>
      <p style="margin:0;">Hôte: '.htmlspecialchars($data['host_name']).' ('.htmlspecialchars($data['host_email']).')</p>

      <h3 style="margin:14px 0 6px;">Détails</h3>
      <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;">
        <tr>
          <td><b>Date début</b></td><td>'.htmlspecialchars($data['start_date']).'</td>
        </tr>
        <tr>
          <td><b>Date fin</b></td><td>'.htmlspecialchars($data['end_date']).'</td>
        </tr>
        <tr>
          <td><b>Guests</b></td><td>'.(int)$data['guests'].'</td>
        </tr>
        <tr>
          <td><b>Prix total</b></td><td>'.htmlspecialchars((string)$data['total_price']).'</td>
        </tr>
        <tr>
          <td><b>Créé le</b></td><td>'.htmlspecialchars((string)$data['created_at']).'</td>
        </tr>
      </table>

      <p style="margin-top:16px;color:#666;font-size:12px;">
        Généré par KARI
      </p>
    </div>';

    (new PdfService())->stream($html, 'recu-reservation-'.$data['id'].'.pdf');
    exit;

} catch (\Throwable $e) {
    echo '<p style="color:red;">'.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p><a href="index.php?page=my_reservations">Retour</a></p>';
}
