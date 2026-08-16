<?php

class SessionManager
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    public static function getPanier(): array
    {
        return self::get('panier', []);
    }

    public static function setPanier(array $panier): void
    {
        self::set('panier', $panier);
    }

    public static function addToPanier(int $produitId, int $quantite): void
    {
        $panier = self::getPanier();
        if (isset($panier[$produitId])) {
            $panier[$produitId] += $quantite;
        } else {
            $panier[$produitId] = $quantite;
        }
        self::setPanier($panier);
    }

    public static function removeFromPanier(int $produitId): void
    {
        $panier = self::getPanier();
        unset($panier[$produitId]);
        self::setPanier($panier);
    }

    public static function clearPanier(): void
    {
        self::setPanier([]);
    }

    public static function getClientId(): ?int
    {
        return self::get('client_id');
    }

    public static function setClientId(?int $clientId): void
    {
        self::set('client_id', $clientId);
    }

    public static function getModeReglement(): string
    {
        return self::get('mode_reglement', 'Wave');
    }

    public static function setModeReglement(string $mode): void
    {
        self::set('mode_reglement', $mode);
    }

    public static function getMontantVerse(): float
    {
        return self::get('montant_verse', 0);
    }

    public static function setMontantVerse(float $montant): void
    {
        self::set('montant_verse', $montant);
    }

    public static function setMessage(string $message, string $type = 'success'): void
    {
        self::set('message', $message);
        self::set('message_type', $type);
    }

    public static function getMessage(): ?array
    {
        $message = self::get('message');
        $type = self::get('message_type');
        if ($message) {
            self::unset('message');
            self::unset('message_type');
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }
}
