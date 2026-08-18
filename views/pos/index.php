<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager Pro - Caisse</title>
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
        .message.warning { background: rgba(251, 191, 36, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.2); }
        .pos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .panel {
            background: rgba(22, 30, 49, 0.65); border: 1px solid rgba(45, 212, 191, 0.12);
            border-radius: 16px; padding: 20px;
        }
        .panel h2 { color: #2dd4bf; font-size: 18px; margin-bottom: 16px; border-left: 4px solid #2dd4bf; padding-left: 12px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .form-control, .form-select {
            width: 100%; padding: 10px 14px;
            background: rgba(8, 12, 24, 0.7); border: 1px solid rgba(45, 212, 191, 0.12);
            border-radius: 10px; color: white; font-size: 14px; outline: none;
        }
        .form-control:focus, .form-select:focus { border-color: #2dd4bf; }
        .form-select option { background: #0b0f19; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .btn {
            padding: 10px 20px; border: none; border-radius: 10px;
            font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none; display: inline-block;
        }
        .btn-primary { background: linear-gradient(135deg, #2dd4bf, #0d9488); color: #0b0f19; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(45, 212, 191, 0.3); }
        .btn-success { background: linear-gradient(135deg, #34d399, #059669); color: white; width: 100%; padding: 14px; font-size: 15px; }
        .btn-success:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(52, 211, 153, 0.3); }
        .btn-danger { background: rgba(248, 113, 113, 0.15); color: #f87171; padding: 4px 10px; font-size: 12px; }
        .btn-danger:hover { background: rgba(248, 113, 113, 0.25); }
        .btn-warning { background: rgba(251, 191, 36, 0.15); color: #fbbf24; padding: 4px 10px; font-size: 12px; }
        .btn-block { display: block; width: 100%; text-align: center; }
        .mt-10 { margin-top: 10px; }
        .mb-10 { margin-bottom: 10px; }
        .text-muted { color: #94a3b8; }
        .text-danger { color: #f87171; }
        .text-success { color: #34d399; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .cart-table { width: 100%; border-collapse: collapse; font-size: 13px; margin: 10px 0; }
        .cart-table th { text-align: left; color: #94a3b8; font-weight: 700; font-size: 11px; text-transform: uppercase; padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .cart-table td { padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.03); }
        .cart-total { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-top: 2px solid #2dd4bf; font-size: 20px; font-weight: 800; }
        .cart-total span:last-child { color: #2dd4bf; }
        .empty-cart { text-align: center; color: #94a3b8; padding: 20px 0; }
        .client-info {
            font-size: 13px; padding: 10px; background: rgba(45, 212, 191, 0.05);
            border-radius: 8px; margin-bottom: 14px; border: 1px solid rgba(45, 212, 191, 0.1);
        }
        .client-info .label { color: #94a3b8; font-size: 11px; text-transform: uppercase; }
        .client-info .value { font-weight: 700; color: white; }
        .product-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
            max-height: 300px; overflow-y: auto; margin-bottom: 14px;
        }
        .product-item {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(45, 212, 191, 0.08);
            border-radius: 10px; padding: 12px; text-align: center; cursor: pointer; width: 100%; color: white;
        }
        .product-item:hover { background: rgba(45, 212, 191, 0.08); border-color: #2dd4bf; }
        .product-item .name { font-weight: 700; font-size: 13px; }
        .product-item .price { color: #2dd4bf; font-weight: 800; font-size: 14px; }
        .product-item .stock { font-size: 11px; color: #94a3b8; }
        .product-item .stock.alert { color: #f87171; }
        .actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>StoreManager Pro - Caisse</h1>
        <div>
            <a href="?action=dettes" class="btn btn-warning">Gestion Dettes</a>
            <a href="?action=clearCart" class="btn btn-danger" onclick="return confirm('Vider le panier ?')">Vider</a>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $message['type'] ?>"><?= htmlspecialchars($message['message']) ?></div>
    <?php endif; ?>

    <div class="pos-grid">
        <div class="panel">
            <h2>Panier</h2>

            <form method="POST" action="?action=setClient">
                <div class="form-group">
                    <label for="client_id">Client</label>
                    <div class="row" style="grid-template-columns: 3fr 1fr;">
                        <select name="client_id" id="client_id" class="form-select">
                            <option value="">-- Selectionner un client --</option>
                            <?php foreach ($clients as $client): ?>
                            <option value="<?= $client->getId() ?>" <?= ($clientId == $client->getId()) ? 'selected' : '' ?>>
                                <?= $client->getNomComplet() ?> (<?= $client->getTelephone() ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </form>

            <?php if ($clientId):
                $client = ClientRepository::selectById($clientId);
                if ($client):
            ?>
            <div class="client-info">
                <div><span class="label">Client :</span> <span class="value"><?= $client->getNomComplet() ?></span></div>
                <div><span class="label">Telephone :</span> <span class="value"><?= $client->getTelephone() ?></span></div>
                <div><span class="label">Limite de credit :</span> <span class="value"><?= number_format($client->getLimiteCredit(), 0, ',', ' ') ?> F</span></div>
                <div><span class="label">Solde disponible :</span> <span class="value <?= ClientRepository::getSoldeDisponible($clientId) <= 0 ? 'text-danger' : 'text-success' ?>">
                    <?= number_format(ClientRepository::getSoldeDisponible($clientId), 0, ',', ' ') ?> F
                </span></div>
            </div>
            <?php endif; endif; ?>

            <div class="form-group">
                <label>Articles</label>
                <div class="product-grid">
                    <?php foreach ($produits as $p): ?>
                    <form method="POST" action="?action=addToCart" style="display:inline;">
                        <input type="hidden" name="produit_id" value="<?= $p->getId() ?>">
                        <input type="hidden" name="quantite" value="1">
                        <button type="submit" class="product-item">
                            <div class="name"><?= $p->getLibelle() ?></div>
                            <div class="price"><?= number_format($p->getPrixVente(), 0, ',', ' ') ?> F</div>
                            <div class="stock <?= $p->isStockLow() ? 'alert' : '' ?>">Stock: <?= $p->getStockActuel() ?></div>
                        </button>
                    </form>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="POST" action="?action=addToCart">
                <div class="row">
                    <div class="form-group">
                        <label for="produit_id">ID Produit</label>
                        <input type="number" name="produit_id" id="produit_id" class="form-control" placeholder="1" min="1">
                    </div>
                    <div class="form-group">
                        <label for="quantite">Quantite</label>
                        <input type="number" name="quantite" id="quantite" class="form-control" value="1" min="1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
            </form>

            <div style="max-height: 200px; overflow-y: auto; margin-top: 14px;">
                <table class="cart-table">
                    <thead><tr><th>Article</th><th style="text-align:center;">Qte</th><th style="text-align:right;">Total</th><th></th></tr></thead>
                    <tbody>
                        <?php if (empty($panier)): ?>
                        <tr><td colspan="4" class="empty-cart">Panier vide</td></tr>
                        <?php else: ?>
                            <?php foreach ($panier as $produitId => $quantite):
                                $produit = ProduitRepository::selectById($produitId);
                                if (!$produit) continue;
                                $sousTotal = $produit->getPrixVente() * $quantite;
                            ?>
                            <tr>
                                <td><?= $produit->getLibelle() ?></td>
                                <td style="text-align:center;"><?= $quantite ?></td>
                                <td style="text-align:right; font-weight:700; color:#2dd4bf;"><?= number_format($sousTotal, 0, ',', ' ') ?> F</td>
                                <td style="text-align:right;"><a href="?action=removeFromCart&id=<?= $produitId ?>" class="btn btn-danger">X</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-total"><span>Total</span><span><?= number_format($total, 0, ',', ' ') ?> F</span></div>

            <form method="POST" action="?action=setPayment">
                <div class="row">
                    <div class="form-group">
                        <label for="mode_reglement">Reglement</label>
                        <select name="mode_reglement" id="mode_reglement" class="form-select">
                            <option value="Wave" <?= $modeReglement == 'Wave' ? 'selected' : '' ?>>Wave</option>
                            <option value="Orange Money" <?= $modeReglement == 'Orange Money' ? 'selected' : '' ?>>Orange Money</option>
                            <option value="Especes" <?= $modeReglement == 'Especes' ? 'selected' : '' ?>>Especes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="montant_verse">Montant verse (F)</label>
                        <input type="number" name="montant_verse" id="montant_verse" class="form-control" value="<?= $montantVerse ?>" min="0" step="100">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning btn-block" style="padding:8px;">Mettre a jour</button>
            </form>

            <div class="mt-10">
                <a href="?action=validateSale" class="btn btn-success btn-block" onclick="return confirm('Confirmer la vente ?')">Valider la vente</a>
            </div>
        </div>

        <div class="panel">
            <h2>Resume</h2>
            <div style="font-size:14px; padding:10px;">
                <p><strong>Client :</strong> 
                    <?php if ($clientId && isset($client)): ?>
                        <?= $client->getNomComplet() ?>
                    <?php else: ?>
                        <span class="text-muted">Non selectionne</span>
                    <?php endif; ?>
                </p>
                <p><strong>Total :</strong> <?= number_format($total, 0, ',', ' ') ?> F</p>
                <p><strong>Verse :</strong> <?= number_format($montantVerse, 0, ',', ' ') ?> F</p>
                <p><strong>Reste :</strong> 
                    <span class="<?= ($total - $montantVerse) > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($total - $montantVerse, 0, ',', ' ') ?> F
                    </span>
                </p>
                <p><strong>Mode de reglement :</strong> <?= $modeReglement ?></p>
            </div>

            <hr style="border-color:rgba(255,255,255,0.05); margin:16px 0;">

            <div style="padding:16px; background:rgba(255,255,255,0.02); border-radius:10px;">
                <h3 style="color:#94a3b8; font-size:13px; text-transform:uppercase; margin-bottom:10px;">Articles dans le panier</h3>
                <?php if (empty($panier)): ?>
                    <div class="text-muted text-center" style="padding:10px;">Aucun article</div>
                <?php else: ?>
                    <ul style="list-style:none; font-size:13px;">
                    <?php foreach ($panier as $produitId => $quantite):
                        $produit = ProduitRepository::selectById($produitId);
                        if (!$produit) continue;
                    ?>
                        <li style="display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px solid rgba(255,255,255,0.03);">
                            <span><?= $produit->getLibelle() ?></span>
                            <span><?= $quantite ?> x <?= number_format($produit->getPrixVente(), 0, ',', ' ') ?> F</span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
