"use client";

import { useEffect, useRef, useState } from "react";

export default function NewsletterPage() {
  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);

  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const [showToast, setShowToast] = useState(false);
  const [toastType, setToastType] = useState<"success" | "info">("success");
  const toastTimer = useRef<number | null>(null);

  useEffect(() => {
    return () => {
      if (toastTimer.current) window.clearTimeout(toastTimer.current);
    };
  }, []);

  function fireToast(msg: string, type: "success" | "info" = "success") {
    setSuccess(msg);
    setToastType(type);
    setShowToast(true);

    if (toastTimer.current) window.clearTimeout(toastTimer.current);
    toastTimer.current = window.setTimeout(() => setShowToast(false), 3500);
  }

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    setSuccess(null);
    setShowToast(false);

    try {
      const res = await fetch("/api/newsletter/subscribe", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });

      const data = await res.json().catch(() => null);

      // ✅ Un seul bloc d'erreur, avec 409 stylé
      if (!res.ok) {
        if (res.status === 409) {
          fireToast(
            data?.error ?? "Cet email est déjà inscrit à la newsletter.",
            "info"
          );
          return;
        }
        throw new Error(data?.error ?? "Erreur lors de l'inscription");
      }

      fireToast(data?.message ?? "Inscription réussie 🎉", "success");
      setEmail("");
    } catch (err: any) {
      setError(err.message ?? "Erreur serveur");
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="container authWrap">
      {/* Toast animé (en haut à droite) */}
      <div
        className={[
          "nlToast",
          showToast ? "nlToast--show" : "nlToast--hide",
          toastType === "info" ? "nlToast--info" : "nlToast--success",
        ].join(" ")}
        role="status"
        aria-live="polite"
      >
        <div className="nlToastIcon" aria-hidden="true">
          {toastType === "info" ? "⚠️" : "✓"}
        </div>

        <div className="nlToastText">
          <div className="nlToastTitle">
            {toastType === "info" ? "Déjà inscrit" : "C’est bon !"}
          </div>
          <div className="nlToastMsg">{success ?? ""}</div>
        </div>

        <button
          className="nlToastClose"
          type="button"
          onClick={() => setShowToast(false)}
          aria-label="Fermer"
        >
          ×
        </button>
      </div>

      <div className="authCard frame">
        <div className="frameHead">
          <div>
            <div className="kicker">Newsletter</div>
            <h1 className="authTitle">Reste informé</h1>
            <p className="authMeta">
              Reçois nos dernières actualités directement dans ta boîte mail.
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

            {error && <p className="authError">{error}</p>}

            {/* Encart succès animé sous le champ (uniquement si succès) */}
            {success && !error && toastType === "success" && (
              <div className="nlSuccessCard" role="status" aria-live="polite">
                <div className="nlSuccessIcon" aria-hidden="true">
                  ✉️
                </div>
                <div>
                  <div className="nlSuccessTitle">Inscription confirmée</div>
                  <div className="nlSuccessText">
                    Merci ! Tu recevras nos emails. (Tu peux te désinscrire à tout
                    moment.)
                  </div>
                </div>
              </div>
            )}

            <div className="authActions">
              <button className="btn btnPrimary" type="submit" disabled={loading}>
                {loading ? "Inscription..." : "S'inscrire"}
              </button>
            </div>
          </form>
        </div>
      </div>

      <style jsx>{`
        /* Toast */
        .nlToast {
          position: fixed;
          top: 18px;
          right: 18px;
          z-index: 9999;
          width: min(420px, calc(100vw - 36px));
          display: flex;
          gap: 12px;
          align-items: center;
          padding: 12px 12px;
          border-radius: 14px;
          background: rgba(20, 20, 20, 0.92);
          color: #fff;
          box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
          backdrop-filter: blur(10px);
          transform-origin: top right;
        }

        .nlToast--success {
          border-left: 4px solid rgba(78, 205, 130, 0.9);
        }
        .nlToast--info {
          border-left: 4px solid rgba(244, 183, 64, 0.95);
        }

        .nlToast--hide {
          opacity: 0;
          transform: translateY(-10px) scale(0.98);
          pointer-events: none;
          transition: opacity 180ms ease, transform 180ms ease;
        }

        .nlToast--show {
          opacity: 1;
          transform: translateY(0) scale(1);
          pointer-events: auto;
          transition: opacity 220ms ease, transform 220ms cubic-bezier(0.2, 0.9, 0.2, 1.2);
          animation: nlPop 420ms cubic-bezier(0.2, 0.9, 0.2, 1.2);
        }

        @keyframes nlPop {
          0% { transform: translateY(-10px) scale(0.96); }
          100% { transform: translateY(0) scale(1); }
        }

        .nlToastIcon {
          width: 34px;
          height: 34px;
          border-radius: 999px;
          display: grid;
          place-items: center;
          font-weight: 700;
          line-height: 1;
          border: 1px solid transparent;
          background: rgba(255, 255, 255, 0.06);
        }

        .nlToast--success .nlToastIcon {
          background: rgba(78, 205, 130, 0.18);
          border-color: rgba(78, 205, 130, 0.35);
        }

        .nlToast--info .nlToastIcon {
          background: rgba(244, 183, 64, 0.16);
          border-color: rgba(244, 183, 64, 0.42);
        }

        .nlToastText {
          flex: 1;
          min-width: 0;
        }

        .nlToastTitle {
          font-weight: 700;
          font-size: 14px;
          margin-bottom: 2px;
        }

        .nlToastMsg {
          font-size: 13px;
          opacity: 0.9;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }

        .nlToastClose {
          width: 32px;
          height: 32px;
          border-radius: 10px;
          border: 0;
          background: rgba(255, 255, 255, 0.08);
          color: #fff;
          cursor: pointer;
          font-size: 18px;
          line-height: 1;
        }

        .nlToastClose:hover {
          background: rgba(255, 255, 255, 0.14);
        }

        /* Success card under field */
        .nlSuccessCard {
          display: flex;
          gap: 12px;
          align-items: flex-start;
          padding: 12px 12px;
          border-radius: 14px;
          margin-top: 10px;
          border: 1px solid rgba(78, 205, 130, 0.28);
          background: rgba(78, 205, 130, 0.12);
          animation: nlSlideFade 360ms ease;
        }

        @keyframes nlSlideFade {
          from { opacity: 0; transform: translateY(-6px); }
          to { opacity: 1; transform: translateY(0); }
        }

        .nlSuccessIcon {
          width: 34px;
          height: 34px;
          border-radius: 12px;
          display: grid;
          place-items: center;
          background: rgba(78, 205, 130, 0.18);
          border: 1px solid rgba(78, 205, 130, 0.3);
        }

        .nlSuccessTitle {
          font-weight: 700;
          font-size: 14px;
          margin-bottom: 2px;
        }

        .nlSuccessText {
          font-size: 13px;
          opacity: 0.9;
        }

        @media (prefers-reduced-motion: reduce) {
          .nlToast--show,
          .nlToast--hide,
          .nlSuccessCard {
            transition: none !important;
            animation: none !important;
          }
        }
      `}</style>
    </main>
  );
}
