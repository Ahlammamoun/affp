"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";

export default function SuccessPage() {
  const router = useRouter();
  const sp = useSearchParams();
  const sessionId = sp.get("session_id");

  const [status, setStatus] = useState<"loading" | "ok" | "pending" | "error">("loading");
  const [msg, setMsg] = useState<string>("");
  useEffect(() => {
    let cancelled = false;

    const sleep = (ms: number) => new Promise((r) => setTimeout(r, ms));

    async function safeText(r: Response) {
      try { return await r.text(); } catch { return ""; }
    }

    async function run() {
      if (!sessionId) {
        setStatus("error");
        setMsg("session_id manquant dans l’URL.");
        return;
      }

      setStatus("loading");
      setMsg("");

      try {
        // Retry confirm (supporte 202 Pending + timeouts/proxy)
        const maxTries = 8;
        for (let i = 0; i < maxTries; i++) {
          if (cancelled) return;

          const r1 = await fetch(`/api/billing/confirm?session_id=${encodeURIComponent(sessionId)}`, {
            method: "POST",
            credentials: "include",
            headers: { "Accept": "application/json" },
          });

          // 202 = pas prêt, on attend et on retente
          if (r1.status === 202) {
            setStatus("pending");
            setMsg("Paiement en cours de confirmation…");
            await sleep(800 + i * 600); // backoff
            continue;
          }

          if (!r1.ok) {
            const ct = r1.headers.get("content-type") || "";
            const body = await safeText(r1);
            const clean =
              ct.includes("text/html")
                ? "Le serveur met trop de temps à répondre (504). Réessaie dans quelques secondes."
                : (body || `Erreur confirmation (${r1.status}).`);
            throw new Error(clean);
          }

          // OK
          const d1 = await r1.json().catch(() => null);
          if (!d1?.ok) {
            // si ton API renvoie {ok:false} quand pas prêt
            setStatus("pending");
            setMsg("Paiement en cours de confirmation…");
            await sleep(800 + i * 600);
            continue;
          }

          // Refresh JWT
          const r2 = await fetch("/api/refresh", { method: "POST", credentials: "include" });
          if (!r2.ok) {
            const body = await safeText(r2);
            throw new Error(body || "Refresh session impossible.");
          }

          if (cancelled) return;
          setStatus("ok");
          router.replace("/");
          router.refresh();
          return;
        }

        // Si on a épuisé les retries
        throw new Error("La confirmation prend plus de temps que prévu. Réessaye dans 1 minute.");
      } catch (e: any) {
        if (cancelled) return;
        setStatus("error");
        setMsg(e?.message ?? "Erreur");
      }
    }

    run();
    return () => { cancelled = true; };
  }, [sessionId, router]);


  return (
    <div className="container" style={{ paddingTop: 30, paddingBottom: 60 }}>
      <section className="frame">
        <div className="frameHead">
          <div className="frameTitle">Paiement</div>
          <div className="frameLink">
            {status === "loading" ? "Traitement…" : status === "ok" ? "OK" : status === "pending" ? "En attente" : "Erreur"}
          </div>
        </div>

        <div className="frameBody" style={{ color: "var(--muted)" }}>
          {status === "loading" && <div>Activation de votre accès premium…</div>}
          {status === "pending" && <div>{msg}</div>}
          {status === "error" && (
            <>
              <div style={{ color: "crimson", fontWeight: 700 }}>{msg}</div>
              <div style={{ marginTop: 12, display: "flex", gap: 10, flexWrap: "wrap" }}>
                <button className="btn btnPrimary" onClick={() => location.reload()}>
                  Réessayer
                </button>
                <Link className="btn" href="/connexion">
                  Me reconnecter
                </Link>
              </div>
            </>
          )}

          {/* Si tu veux garder un lien manuel */}
          <div style={{ marginTop: 12 }}>
            <Link className="btn" href="/">
              Retour accueil
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
