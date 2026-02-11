import Link from "next/link";
import { notFound } from "next/navigation";
import { apiGet } from "../../lib/api";

type Item = {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  publishedAt: string | null;
  createdAt: string | null;
  thumb?: string | null; // ✅
};

type Resp = {
  page: number;
  limit: number;
  total: number;
  items: Item[];
  section: { name: string; slug: string };
};

export default async function SectionPage({
  params,
}: {
  params: Promise<{ slug?: string }>;
}) {
  const { slug } = await params;

  if (!slug || slug === "undefined") return notFound();

  let data: Resp;
  try {
    data = await apiGet<Resp>(`/sections/${encodeURIComponent(slug)}/articles`);
  } catch (e: any) {
    const msg = String(e?.message ?? "");
    if (msg.includes("404")) return notFound();
    throw e;
  }

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 40 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
        </div>

        <section className="frame">
          <div className="frameBody">
            <div className="kicker">Rubrique</div>
            <h1 className="titleXL" style={{ marginTop: 10 }}>
              {data.section?.name ?? slug}
            </h1>
            <div className="meta">{data.total ?? 0} article(s)</div>
          </div>
        </section>

        <div style={{ display: "grid", gap: 14 }}>
          {data.items?.map((a) => (
            <section key={a.id} className="frame">
              <div
                className="frameBody"
                style={{
                  display: "grid",
                  gridTemplateColumns: "1fr 160px",
                  gap: 14,
                  alignItems: "start",
                }}
              >
                {/* Colonne gauche : titre + excerpt */}
                <div>
                  <h2 style={{ margin: 0, fontSize: 20, lineHeight: 1.25 }}>
                    <Link href={`/article/${a.slug}`}>{a.title}</Link>
                  </h2>

                  <div
                    style={{
                      marginTop: 10,
                      color: "var(--muted)",
                      lineHeight: 1.6,
                    }}
                    dangerouslySetInnerHTML={{ __html: a.excerpt }}
                  />
                </div>

                {/* Colonne droite : photo */}
                {a.thumb ? (
                  <div
                    style={{
                      width: 160,
                      height: 110,
                      overflow: "hidden",
                      borderRadius: 10,
                      border: "1px solid rgba(0,0,0,.08)",
                      background: "rgba(0,0,0,.06)",
                    }}
                  >
                    <img
                      src={a.thumb}
                      alt={a.title}
                      loading="lazy"
                      style={{
                        width: "100%",
                        height: "100%",
                        objectFit: "cover",
                        display: "block",
                      }}
                    />
                  </div>
                ) : (
                  <div />
                )}
              </div>
            </section>
          ))}

          {(!data.items || data.items.length === 0) && (
            <div style={{ color: "var(--muted)" }}>
              Aucun article publié dans cette rubrique.
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
