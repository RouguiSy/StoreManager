<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager Pro - Gestion des Dettes</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #0b0f19; color: #f8fafc; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; padding: 16px 24px;
            background: rgba(22, 30, 49, 0.65); border-radius: 16px;
            border: 1px solid rgba(45, 212, 191, 0.12);
        }
        .header h1 { color: #2dd4bf; font-size: 24px; }
        .header a { color: #94a3b8; text-decoration: none; padding: 8px 16px; border-radius: 8px; background: rgba(255,255,255,0.05); }
        .header a:hover { background: rgba(255,255,255,0.1); }
        .message {
            padding: 12px 20px; border-radius: 10px; margin-bottom: 16px; font-weight: 700;
        }
        .message.success { background: rgba(52, 211, 153, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.2); }
        .message.error { background: rgba(248, 113, 113, 0.15); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.2); }
        .panel {
            background: rgba(22, 30, 49, 0.65); border: 1px solid rgba(45, 212, 191, 0.12);
            border-radius: 16px; padding: 20px;
        }
        .panel h2 { color: #2dd4bf; font-size: 18px; margin-bottom: 16px; border-left: 4px solid #2dd4bf; padding-left: 12px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-card {
            background: rgba(22, 30, 49, 0.65); border: 1px solid rgba(45, 212, 191, 0.12);
            border-radius: 12px; padding: 16px; text-align: center;
        }
        .stat-card .value { font-size: 24px; font-weight: 800; color: #2dd4bf; }
        .stat-card .label { font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; color: #94a3b8; font-size: 11px; text-transform: uppercase; font-weight: 700; padding: 10px 8px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        table td { padding: 10px 8px; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 13px; }
        .badge {
            padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;
        }
        .badge-soldee { background: rgba(52, 211, 153, 0.15); color: #34d399; }
        .badge-non-soldee { background: rgba(248, 113, 113, 0.15); color: #f87171; }
        .btn {
            padding: 6px 14px; border: none; border-radius: 8px; font-weight: 700; font-size: 12px;
            cursor: pointer; text-decoration: none; display: inline-block;
        }
        .btn-success { background: #34d399; color: white; }
        .btn-success:hover { opacity: 0.8; }
        .btn-primary { background: #2dd4bf; color: #0b0f19; }
        .btn-danger { background: rgba(248, 113, 113, 0.15); color: #f87171; }
        .btn-sm { padding: 4px 10px; font-size: 11px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 12px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .form-control, .form-select {
            width: 100%; padding: 10px 14px; background: rgba(8, 12, 24, 0.7);
            border: 1px solid rgba(45, 212, 191, 0.12); border-radius: 8px; color: white; font-size: 14px; outline: none;
        }
        .form-control:focus, .form-select:focus { border-color: #2dd4bf; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .mt-10 { margin-top: 10px; }
        .text-muted { color: #94a3b8; }
        .text-danger { color: #f87171; }
        .text-success { color: #34d399; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .drawer {
            display: none; padding: 16px; background: rgba(255,255,255,0.02);
            border-radius: 10px; margin-top: 10px; border: 1px solid rgba(255,255,255,0.05);
        }
        .drawer.open { display: block; }
        .client-link { color: #2dd4bf; text-decoration: none; }
        .client-link:hover { text-decoration: underline; }
    </style>
    <script>
        function toggleDrawer(id) {
            var drawer = document.getElementById(id);
            if (drawer) { drawer.classList.toggle('open'); }
        }
    </script>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Gestion des Dettes</h1>
        <div>
            <a href="?action=index">Retour a la Caisse</a>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $message['type'] ?>"><?= htmlspecialchars($message['message']) ?></div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat-card"><div class="value"><?= number_format($totalDettes, 0, ',', ' ') ?> F</div><div class="label">Total des Dettes</div></div>
        <div class="stat-card"><div class="value"><?= $nombreDettes ?></div><div class="label">Nombre de Dettes</div></div>
        <div class="stat-card"><div class="value"><?= $nombreClientsDebiteurs ?></div><div class="label">Clients Debiteurs</div></div>
        <div class="stat-card"><div class="value"><?= number_format($totalRembourse, 0, ',', ' ') ?> F</div><div class="label">Total Rembourse</div></div>
    </div>

    <div class="panel">
        <div class="flex-between">
            <h2>Liste des Dettes Actives</h2>
            <div>
                <a href="?action=dettes" class="btn btn-sm btn-primary">Toutes</a>
            </div>
        </div>

        <?php if (empty($dettes)): ?>
        <p class="text-muted text-center" style="padding: 20px 0;">Aucune dette active</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Client</th>
                    <th>Montant Initial</th>
                    <th>Rembourse</th>
                    <th>Reste Du</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dettes as $dette):
                    $client = ClientRepository::selectById($dette->getClientId());
                ?>
                <tr>
                    <td><strong><?= $dette->getRef() ?></strong></td>
                    <td>
                        <?php if ($client): ?>
                        <a href="#" class="client-link"><?= $client->getNomComplet() ?></a>
                        <?php else: ?>
                        <span class="text-muted">Client inconnu</span>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($dette->getMontantInitial(), 0, ',', ' ') ?> F</td>
                    <td class="text-success"><?= number_format($dette->getMontantVerse(), 0, ',', ' ') ?> F</td>
                    <td class="text-danger"><?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> F</td>
                    <td>
                        <span class="badge badge-<?= $dette->getStatut() === 'SOLDEE' ? 'soldee' : 'non-soldee' ?>">
                            <?= $dette->getStatut() === 'SOLDEE' ? 'SOLDEE' : 'NON SOLDEE' ?>
                        </span>
                    </td>
                    <td>
                        <button onclick="toggleDrawer('paiement-<?= $dette->getId() ?>')" class="btn btn-sm btn-success">Rembourser</button>
                        <button onclick="toggleDrawer('historique-<?= $dette->getId() ?>')" class="btn btn-sm btn-primary">Historique</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 0;">
                        <div id="paiement-<?= $dette->getId() ?>" class="drawer">
                            <h4 style="color: #2dd4bf; margin-bottom: 10px;">Remboursement de la dette <?= $dette->getRef() ?></h4>
                            <p class="text-muted" style="margin-bottom: 10px;">
                                Reste du : <strong class="text-danger"><?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> FCFA</strong>
                            </p>
                            <form method="POST" action="?action=repay">
                                <input type="hidden" name="dette_id" value="<?= $dette->getId() ?>">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="montant_<?= $dette->getId() ?>">Montant (FCFA)</label>
                                        <input type="number" name="montant" id="montant_<?= $dette->getId() ?>" class="form-control" max="<?= $dette->getMontantRestant() ?>" value="<?= $dette->getMontantRestant() ?>" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mode_<?= $dette->getId() ?>">Mode de Paiement</label>
                                        <select name="mode_paiement_id" id="mode_<?= $dette->getId() ?>" class="form-select" required>
                                            <?php foreach ($modesPaiement as $mode): ?>
                                            <option value="<?= $mode['id'] ?>"><?= $mode['nom'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="notes_<?= $dette->getId() ?>">Notes (optionnel)</label>
                                    <input type="text" name="notes" id="notes_<?= $dette->getId() ?>" class="form-control" placeholder="Reference ou commentaire">
                                </div>
                                <button type="submit" class="btn btn-success">Valider le Remboursement</button>
                                <button type="button" onclick="toggleDrawer('paiement-<?= $dette->getId() ?>')" class="btn btn-danger">Annuler</button>
                            </form>
                        </div>

                        <div id="historique-<?= $dette->getId() ?>" class="drawer">
                            <h4 style="color: #2dd4bf; margin-bottom: 10px;">Historique des paiements - <?= $dette->getRef() ?></h4>
                            <?php
                            $paiements = DebtService::getPaiementsByDette($dette->getId());
                            if (empty($paiements)):
                            ?>
                            <p class="text-muted">Aucun paiement enregistre</p>
                            <?php else: ?>
                            <table>
                                <thead><tr><th>Date</th><th>Montant</th><th>Notes</th></tr></thead>
                                <tbody>
                                    <?php foreach ($paiements as $p): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($p['date_paiement'])) ?></td>
                                        <td class="text-success"><?= number_format($p['montant'], 0, ',', ' ') ?> F</td>
                                        <td class="text-muted"><?= $p['notes'] ?? '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                            <button type="button" onclick="toggleDrawer('historique-<?= $dette->getId() ?>')" class="btn btn-danger btn-sm mt-10">Fermer</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
