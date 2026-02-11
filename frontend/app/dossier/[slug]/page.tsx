import Link from "next/link";
import { notFound } from "next/navigation";
import { apiGet } from "../../lib/api";
import LikeButton from "../../components/DossierLikeButton";

type Article = { id: number; title: string; slug: string };

type Resp = {
  item: {
    id: number;
    title: string;
    slug: string;
    lead: string | null;
    content: string | null;      // HTML possible
    conclusion: string | null;
    author: { name: string | null; bio: string | null };
    thumb: string | null;
    status: string;
    publishedAt: string | null;
    createdAt: string;
    updatedAt: string | null;
    articles: Article[];
  };
};

export default async function DossierPage({
  params,
}: {
  params: Promise<{ slug?: string }>;
}) {
  const { slug } = await params;

  if (!slug || slug === "undefined") return notFound();

  let data: Resp;
  try {
    data = await apiGet<Resp>(`/dossiers/${encodeURIComponent(slug)}`);
  } catch (e: any) {
    const msg = String(e?.message ?? "");
    if (msg.includes("404")) return notFound();
    throw e;
  }

  const d = data.item;

  return (
    <div className="container" style={{ paddingBottom: 22, paddingTop: 20 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
                <LikeButton slug={slug} />
        </div>


        <section className="frame">
          <div className="frameBody">
            <div className="kicker">
              {d.author?.name ?? "Rédaction"}
              {d.publishedAt ? ` • ${new Date(d.publishedAt).toLocaleDateString("fr-FR")}` : ""}
              {d.updatedAt ? ` • MAJ ${new Date(d.updatedAt).toLocaleDateString("fr-FR")}` : ""}
            </div>

            <h1 className="titleXL" style={{ marginTop: 10 }}>
              {d.title}
            </h1>

            {d.lead ? (
              <div style={{ marginTop: 12, color: "var(--muted)", lineHeight: 1.8 }}>
                {d.lead}
              </div>
            ) : null}

            {d.thumb ? (
              <div style={{ marginTop: 14 }}>
                <img
                  src={d.thumb}
                  alt={d.title}
                  loading="lazy"
                  style={{
                    width: "100%",
                    height: 320,
                    objectFit: "cover",
                    borderRadius: 10,
                    border: "1px solid var(--line)",
                    display: "block",
                  }}
                />
              </div>
            ) : null}

            {d.content ? (
              <div
                style={{ marginTop: 16, lineHeight: 1.9 }}
                dangerouslySetInnerHTML={{ __html: d.content }}
              />
            ) : (
              <div style={{ marginTop: 16, color: "var(--muted)" }}>
                Contenu à venir.
              </div>
            )}

            {d.conclusion ? (
              <div style={{ marginTop: 22 }}>
                <h3 style={{ margin: 0 }}>Conclusion</h3>
                <div style={{ marginTop: 10, lineHeight: 1.9 }}>{d.conclusion}</div>
              </div>
            ) : null}

            {d.author?.bio ? (
              <div style={{ marginTop: 22, color: "var(--muted)", lineHeight: 1.8 }}>
                <strong>À propos de l’auteur :</strong> {d.author.bio}
              </div>
            ) : null}
          </div>
        </section>

        <section className="frame">
          <div className="frameHead">
            <div className="frameTitle">Articles du dossier</div>
            <div className="frameLink">{d.articles?.length ?? 0}</div>
          </div>

          <div className="frameBody">
            <div className="list">
              {(d.articles ?? []).map((a) => (
                <div className="listItem" key={a.id}>
                  <Link href={`/article/${a.slug}`}>{a.title}</Link>
                </div>
              ))}

              {(!d.articles || d.articles.length === 0) && (
                <div style={{ color: "var(--muted)" }}>
                  Aucun article lié à ce dossier.
                </div>
              )}
            </div>
          </div>
        </section>
      </div>
    </div>
      );
}
