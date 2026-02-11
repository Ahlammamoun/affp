"use client";

import Link from "next/link";
import { useState } from "react";
import { useRouter } from "next/navigation";

export default function ConnexionPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const res = await fetch("/api/login_check", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      if (!res.ok) {
        const data = await res.json().catch(() => null);
        throw new Error(data?.message ?? "Identifiants incorrects");
      }

      router.replace("/");
      router.refresh();
    } catch (err: any) {
      setError(err.message ?? "Erreur de connexion");
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
            <h1 className="authTitle">Connexion</h1>
            <p className="authMeta">
              Connecte-toi pour accéder à ton espace personnel.
            </p>
          </div>
        </div>

        <div className="frameBody">
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


            <label className="authField">
              <span className="smallMuted">Mot de passe</span>
              <input
                className="authInput"
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />

              <div style={{ marginTop: 6 }}>
                <Link href="/forgot-password" className="smallMuted">
                  Mot de passe oublié ?
                </Link>
              </div>
            </label>

            {error && <p className="authError">{error}</p>}

            <div className="authActions">
              <button
                className="btn btnPrimary"
                type="submit"
                disabled={loading}
              >
                {loading ? "Connexion..." : "Se connecter"}
              </button>

              <Link className="btn" href="/abonnement">
                Créer un compte
              </Link>
            </div>
          </form>
        </div>
      </div>
    </main>
  );
}
