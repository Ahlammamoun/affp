"use client";

import Link from "next/link";
import { useState } from "react";

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);
  const [done, setDone] = useState(false);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);

    try {
      const res = await fetch("/api/password/forgot", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });

      // Ton back renvoie ok:true même si email inexistant (anti-énumération)
      await res.json().catch(() => null);
      setDone(true);
    } catch {
      // Même en cas d’erreur réseau, on reste générique
      setDone(true);
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
            <h1 className="authTitle">Mot de passe oublié</h1>
            <p className="authMeta">
              Saisis ton email pour recevoir un lien de réinitialisation.
            </p>
          </div>
        </div>

        <div className="frameBody">
          {done ? (
            <div className="authForm">
              <p className="smallMuted">
                Si un compte existe pour cet email, un lien de réinitialisation
                vient d’être envoyé.
              </p>

              <div className="authActions">
                <Link className="btn btnPrimary" href="/connexion">
                  Retour à la connexion
                </Link>
              </div>
            </div>
          ) : (
            <form onSubmit={onSubmit} className="authForm">
              <label className="authField">
                <span className="smallMuted">Email</span>
                <input
                  className="authInput"
                  type="email"
                  placeholder="ex: nom@email.com"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                />
              </label>

              <div className="authActions">
                <button
                  className="btn btnPrimary"
                  type="submit"
                  disabled={loading}
                >
                  {loading ? "Envoi..." : "Envoyer le lien"}
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
