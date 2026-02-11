import Link from "next/link";
import { apiGet } from "../lib/api";

type DossierListItem = {
  id: number;
  title: string;
  slug: string;
  thumb: string | null;
  publishedAt: string | null;
  updatedAt: string | null;
  author?: { name?: string | null } | null;
  articlesCount?: number | null;
};

type Resp = {
  items: DossierListItem[];
  page?: number;
  limit?: number;
  total?: number;
  pages?: number;
};

export default async function DossiersPage({
  searchParams,
}: {
  searchParams: Promise<{ page?: string; q?: string }>;
}) {
  const sp = await searchParams;

  const pageNum = Math.max(1, parseInt(sp.page ?? "1", 10) || 1);
  const q = (sp.q ?? "").trim();

  const qs = new URLSearchParams();
  qs.set("page", String(pageNum));
  qs.set("limit", "12");
  qs.set("status", "published");
  qs.set("order", "desc");
  if (q) qs.set("q", q);

  const data = await apiGet<Resp>(`/dossiers?${qs.toString()}`);
  const items = data.items ?? [];

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 60 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
        </div>

        <section className="frame">
          <div className="frameHead"></div>

          <div className="frameBody">
            <div className="kicker">
              {typeof data.total === "number" ? `${data.total} dossiers` : "Tous les dossiers"}
              {q ? ` • “${q}”` : ""}
            </div>

            {/* Recherche */}
            <form action="/dossiers" method="get" style={{ marginTop: 12 }}>
              <div style={{ position: "relative" }}>
                <input
                  name="q"
                  defaultValue={q}
                  placeholder="Rechercher un dossier…"
                  style={{
                    width: "100%",
                    padding: "10px 40px 10px 12px", // espace pour la croix
                    borderRadius: 10,
                    border: "1px solid var(--line)",
                    background: "transparent",
                    color: "var(--text)",
                  }}
                />

                {q && (
                  <Link
                    href="/dossiers"
                    aria-label="Effacer la recherche"
                    style={{
                      position: "absolute",
                      right: 12,
                      top: "50%",
                      transform: "translateY(-50%)",
                      color: "var(--muted)",
                      textDecoration: "none",
                      fontSize: 16,
                      lineHeight: 1,
                      cursor: "pointer",
                    }}
                  >
                    ✕
                  </Link>
                )}
              </div>

              <input type="hidden" name="page" value="1" />
            </form>


            <div className="list" style={{ marginTop: 14 }}>
              {items.map((d) => {
                const dt = d.updatedAt ?? d.publishedAt;
                const dateLabel = dt ? new Date(dt).toLocaleDateString("fr-FR") : null;
                const author = d.author?.name ?? null;

                return (
                  <div className="listItem" key={d.id}>
                    <div style={{ display: "grid", gap: 6 }}>
                      {/* IMPORTANT : route détail = /dossier/[slug] */}
                      <Link href={`/dossier/${d.slug}`}>{d.title}</Link>

                      <span className="smallMuted">
                        {author ? author : "Rédaction"}
                        {dateLabel ? ` • MAJ ${dateLabel}` : ""}
                        {typeof d.articlesCount === "number" ? ` • ${d.articlesCount} articles` : ""}
                      </span>
                    </div>
                  </div>
                );
              })}

              {items.length === 0 && (
                <div style={{ color: "var(--muted)" }}>
                  Aucun dossier publié pour le moment.
                </div>
              )}
            </div>

            {/* Pagination */}
            <div style={{ display: "flex", justifyContent: "space-between", marginTop: 16 }}>
              <div>
                {pageNum > 1 ? (
                  <Link
                    className="frameLink"
                    href={`/dossiers?${new URLSearchParams({
                      page: String(pageNum - 1),
                      ...(q ? { q } : {}),
                    }).toString()}`}
                  >
                    ← Page précédente
                  </Link>
                ) : (
                  <span />
                )}
              </div>

              <div className="meta">
                Page {pageNum}
                {typeof data.pages === "number" ? ` / ${data.pages}` : ""}
              </div>

              <div>
                {typeof data.pages === "number" ? (
                  pageNum < data.pages ? (
                    <Link
                      className="frameLink"
                      href={`/dossiers?${new URLSearchParams({
                        page: String(pageNum + 1),
                        ...(q ? { q } : {}),
                      }).toString()}`}
                    >
                      Page suivante →
                    </Link>
                  ) : (
                    <span />
                  )
                ) : items.length === 12 ? (
                  <Link
                    className="frameLink"
                    href={`/dossiers?${new URLSearchParams({
                      page: String(pageNum + 1),
                      ...(q ? { q } : {}),
                    }).toString()}`}
                  >
                    Page suivante →
                  </Link>
                ) : (
                  <span />
                )}
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}