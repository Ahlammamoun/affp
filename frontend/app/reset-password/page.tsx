"use client";

import Link from "next/link";
import { useMemo, useState } from "react";
import { useRouter } from "next/navigation";

function getTokenFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return params.get("token") || "";
}

export default function ResetPasswordPage() {
  const router = useRouter();
  const token = useMemo(() => getTokenFromUrl(), []);

  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [ok, setOk] = useState(false);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);

    if (!token) {
      setError("Token manquant dans l’URL.");
      return;
    }
    if (password.length < 6) {
      setError("Mot de passe trop court (min 6).");
      return;
    }
    if (password !== confirm) {
      setError("Les mots de passe ne correspondent pas.");
      return;
    }

    setLoading(true);
    try {
      const res = await fetch("/api/password/reset", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ token, password }),
      });

      const data = await res.json().catch(() => null);

      if (!res.ok) {
        throw new Error(data?.message ?? "Erreur lors de la réinitialisation");
      }

      setOk(true);

      // option: redirection auto vers connexion après succès
      setTimeout(() => {
        router.replace("/connexion");
        router.refresh();
      }, 800);
    } catch (err: any) {
      setError(err.message ?? "Erreur réseau");
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="container authWrap">
      <div className="authCard frame">
        <div className="frameHead">
          <div>
            <div className="kicker">Espace membre</div>
            <h1 className="authTitle">Nouveau mot de passe</h1>
            <p className="authMeta">
              Choisis un nouveau mot de passe pour ton compte.
            </p>
          </div>
        </div>

        <div className="frameBody">
          {ok ? (
            <div className="authForm">
              <p className="smallMuted">
                ✅ Mot de passe modifié. Redirection vers la connexion…
              </p>
              <div className="authActions">
                <Link className="btn btnPrimary" href="/connexion">
                  Aller à la connexion
                </Link>
              </div>
            </div>
          ) : (
            <form onSubmit={onSubmit} className="authForm">
              {!token && (
                <p className="authError">
                  Token absent. Ouvre cette page via le lien reçu par email.
                </p>
              )}

              <label className="authField">
                <span className="smallMuted">Nouveau mot de passe</span>
                <input
                  className="authInput"
                  id="new-password"
                  name="newPassword"
                  type="password"
                  autoComplete="new-password"
                  placeholder="••••••••"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                />

                <input
                  className="authInput"
                  id="confirm-password"
                  name="confirmPassword"
                  type="password"
                  autoComplete="new-password"
                  placeholder="••••••••"
                  value={confirm}
                  onChange={(e) => setConfirm(e.target.value)}
                  required
                />

              </label>


              {error && <p className="authError">{error}</p>}

              <div className="authActions">
                <button
                  className="btn btnPrimary"
                  type="submit"
                  disabled={loading || !token}
                >
                  {loading ? "Validation..." : "Changer le mot de passe"}
                </button>

                <Link className="btn" href="/connexion">
                  Annuler
                </Link>
              </div>
            </form>
          )}
        </div>
      </div>
    </main>
  );
}
