"use client";

import Link from "next/link";
import { useState } from "react";
import { useRouter } from "next/navigation";

type ApiErr = { message?: string; detail?: string };

async function readError(res: Response): Promise<string> {
  const ct = res.headers.get("content-type") || "";
  if (ct.includes("application/json")) {
    const j = (await res.json().catch(() => null)) as ApiErr | null;
    return (
      (typeof j?.detail === "string" && j.detail.trim()) ||
      (typeof j?.message === "string" && j.message.trim()) ||
      `Erreur (${res.status})`
    );
  }
  const t = await res.text().catch(() => "");
  return t?.trim() || `Erreur (${res.status})`;
}

export default function AbonnementPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [password2, setPassword2] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const payload = { email: String(email).trim(), password: String(password) };
  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);

    if (password !== password2) {
      setError("Les mots de passe ne correspondent pas.");
      return;
    }

    setLoading(true);
    try {
      // 1) REGISTER (Symfony)
      const r1 = await fetch("/api/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      if (!r1.ok) {
        throw new Error(await readError(r1));
      }

      // 2) LOGIN (Lexik JWT -> renvoie { token })
      const r2 = await fetch("/api/login_check", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        body: JSON.stringify(payload),
        cache: "no-store",
      });

      if (!r2.ok) {
        const txt = await r2.text().catch(() => "");
        throw new Error(txt || `Login échoué (${r2.status})`);
      }

      const loginData = await r2.json().catch(() => null);
      const token = loginData?.token;
      if (!token) throw new Error("Token JWT introuvable après login.");

      // 3) STRIPE CHECKOUT (protégé -> nécessite Authorization)
      const r3 = await fetch("/api/billing/checkout", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (!r3.ok) {
        throw new Error(await readError(r3));
      }

      const data3 = await r3.json().catch(() => null) as any;
      const url = data3?.url;
      if (!url) throw new Error("URL Stripe introuvable.");

      window.location.href = url;
    } catch (err: any) {
      setError(err?.message ?? "Erreur");
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
            <h1 className="authTitle">S’abonner</h1>
            <p className="authMeta">Crée ton compte puis procède au paiement.</p>
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
                autoComplete="email"
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
                autoComplete="new-password"
              />
            </label>

            <label className="authField">
              <span className="smallMuted">Confirmer le mot de passe</span>
              <input
                className="authInput"
                type="password"
                placeholder="••••••••"
                value={password2}
                onChange={(e) => setPassword2(e.target.value)}
                required
                autoComplete="new-password"
              />
            </label>

            {error && <p className="authError">{error}</p>}

            <div className="authActions">
              <button className="btn btnPrimary" type="submit" disabled={loading}>
                {loading ? "Redirection..." : "Créer le compte et payer"}
              </button>

              <Link className="btn" href="/connexion">
                J’ai déjà un compte
              </Link>
            </div>
          </form>
        </div>
      </div>
    </main>
  );
}
