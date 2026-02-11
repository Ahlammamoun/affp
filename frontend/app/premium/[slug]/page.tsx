import Link from "next/link";
import { apiGet } from "../../lib/api";

type PremiumBrief = {
  id: number;
  title: string;
  slug: string;
  scope: string | null;
  scopeLabel: string | null;
  tags: string[] | null;
  bullets: string[] | null;
  summaryHtml: string | null;
  publishedAt: string | null;
  createdAt: string;
  updatedAt: string | null;
};

export default async function PremiumBriefPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;

  const data = await apiGet<{ item: PremiumBrief }>(`/premium-briefs/${slug}`);
  const p = data.item;

  const dt = p.publishedAt
    ? new Date(p.publishedAt).toLocaleString("fr-FR")
    : null;

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 40 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center", flexWrap: "wrap" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
          <Link className="pill" href="/premium">
            Tous les résumés premium
          </Link>
        </div>

        <section className="frame" style={{ borderColor: "var(--accent)" }}>
          <div className="frameBody">
            <div
              style={{
                fontSize: "0.75rem",
                letterSpacing: "0.08em",
                textTransform: "uppercase",
                color: "var(--accent)",
                fontWeight: 800,
              }}
            >
              ⭐ Résumé premium
              {(p.scopeLabel || p.scope) ? ` • ${p.scopeLabel || p.scope}` : ""}
              {dt ? ` • ${dt}` : ""}
            </div>

            <h1 className="titleXL" style={{ marginTop: 10 }}>
              {p.title}
            </h1>

            {p.bullets?.length ? (
              <ul style={{ marginTop: 14, paddingLeft: 18, lineHeight: 1.7 }}>
                {p.bullets.filter(Boolean).map((b, i) => (
                  <li key={i}>{b}</li>
                ))}
              </ul>
            ) : null}

            {p.summaryHtml ? (
              <div
                style={{ marginTop: 16, color: "var(--muted)", lineHeight: 1.8 }}
                dangerouslySetInnerHTML={{ __html: p.summaryHtml }}
              />
            ) : (
              <div style={{ marginTop: 16, color: "var(--muted)" }}>
                Aucun contenu premium.
              </div>
            )}
          </div>
        </section>
      </div>
    </div>
  );
}
