"use client";

import { useState } from "react";
import Link from "next/link";

export default function PremiumButtons() {
  const [okMentions, setOkMentions] = useState(false);
  const [okConditions, setOkConditions] = useState(false);

  const canSubscribe = okMentions && okConditions;

  return (
    <>
      {/* CHECKBOX */}
      <div style={{ display: "grid", gap: 6 }}>
        <label style={{ display: "flex", gap: 8 }}>
          <input
            type="checkbox"
            checked={okMentions}
            onChange={(e) => setOkMentions(e.target.checked)}
          />
          <span>
            J’ai lu les{" "}
            <Link href="/mentions-legales" className="frameLink">
              mentions légales
            </Link>
          </span>
        </label>

        <label style={{ display: "flex", gap: 8 }}>
          <input
            type="checkbox"
            checked={okConditions}
            onChange={(e) => setOkConditions(e.target.checked)}
          />
          <span>
            J’accepte les{" "}
            <Link href="/conditions-abonnement" className="frameLink">
              conditions d’abonnement
            </Link>
          </span>
        </label>
      </div>

      {/* BOUTONS */}
      <div style={{ display: "flex", gap: 10, flexWrap: "wrap", marginTop: 10 }}>
        <Link className="btn" href="/connexion">
          Se connecter
        </Link>

        <Link
          className="btn btnPrimary"
          href={canSubscribe ? "/abonnement" : "#"}
          style={{
            opacity: canSubscribe ? 1 : 0.45,
            pointerEvents: canSubscribe ? "auto" : "none",
            filter: canSubscribe ? "none" : "grayscale(70%)",
          }}
        >
          S’abonner
        </Link>

        <Link className="frameLink" href="/">
          ← Retour à l’accueil
        </Link>
      </div>
    </>
  );
}

