import Link from "next/link";
import { apiGetServer } from "../lib/api.server";
import { cookies } from "next/headers";
import PremiumButtons from "./PremiumButtons";


type PremiumBriefListItem = {
  id: number;
  title: string;
  slug: string;
  scope: string | null;
  scopeLabel: string | null;
  tags: string[] | null;
  bullets: string[] | null;
  publishedAt: string | null;
};

export default async function PremiumBriefsPage() {
  const token = (await cookies()).get("auth_token")?.value;

  // 🔒 PAS CONNECTÉ → MESSAGE (PAS DE REDIRECTION)
  if (!token) {
    return (
      <div className="container" style={{ paddingTop: 40, paddingBottom: 60 }}>
        <section className="frame" style={{ maxWidth: 520, margin: "0 auto" }}>
          <div className="frameHead">
            <div className="frameTitle">Accès réservé</div>
            <div className="frameLink">Premium</div>
          </div>

          <div className="frameBody" style={{ display: "grid", gap: 14 }}>
            <p style={{ color: "var(--muted)", lineHeight: 1.6 }}>
              🔒 Les résumés premium sont réservés aux abonnés.
              <br />
              Connectez-vous ou abonnez-vous pour accéder à l’analyse complète.
            </p>

           <PremiumButtons />

          </div>
        </section>
      </div>
    );
  }

  // ✅ CONNECTÉ → CONTENU NORMAL
  const data = await apiGetServer<{ items: PremiumBriefListItem[] }>(
    `/premium-briefs?limit=24`
  );

  const items = data.items ?? [];

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 50 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
        </div>

        <section className="frame">
          <div className="frameHead">
            <div className="frameTitle">Résumés Premium</div>
            <div className="frameLink">Réservé abonnés</div>
          </div>

          <div className="frameBody">
            {items.length ? (
              <div className="cards">
                {items.map((p) => {
                  const dt = p.publishedAt
                    ? new Date(p.publishedAt).toLocaleString("fr-FR")
                    : null;

                  const kickerParts = [
                    p.scopeLabel || p.scope || "Brief",
                    ...(p.tags?.slice(0, 3) ?? []),
                  ].filter(Boolean);

                  const teaser =
                    p.bullets && p.bullets.length
                      ? p.bullets[0]
                      : "Résumé premium — cliquez pour lire.";

                  return (
                    <article key={p.id} className="frame">
                      <div className="cardPad">
                        <div className="kicker">
                          {kickerParts.join(" • ")}
                          {dt ? ` • ${dt}` : ""}
                        </div>

                        <div className="cardTitle" style={{ marginTop: 8 }}>
                          <Link href={`/premium/${p.slug}`}>{p.title}</Link>
                        </div>

                        <div
                          style={{
                            marginTop: 8,
                            color: "var(--muted)",
                            lineHeight: 1.6,
                          }}
                        >
                          {teaser}
                        </div>

                        <div style={{ marginTop: 10 }}>
                          <Link className="frameLink" href={`/premium/${p.slug}`}>
                            Lire →
                          </Link>
                        </div>
                      </div>
                    </article>
                  );
                })}
              </div>
            ) : (
              <div style={{ color: "var(--muted)" }}>
                Aucun résumé premium publié pour le moment.
              </div>
            )}
          </div>
        </section>
      </div>
    </div>
  );
}
