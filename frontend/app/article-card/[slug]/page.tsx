import Link from "next/link";
import { apiGet } from "../../lib/api";

type ArticleCardItem = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;      // peut être HTML si tu veux
  thumb: string | null;        // url ou "/uploads/..."
  author: string | null;
  publishedAt: string | null;
  link: string | null;
};

export default async function ArticleCardPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;

  const data = await apiGet<{ item: ArticleCardItem }>(`/article-cards/${slug}`);
  const c = data.item;

  const dateLabel = c.publishedAt ?? null;

  // si thumb est un fileName côté backend, adapte ici.
  // là on suppose que c.thumb est déjà une URL/chemin correct.
  const mediaUrl = c.thumb ?? null;

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 40 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>

          {/* option : retour à la liste des cards */}
          <Link className="pill" href="/articles-card">
            Toutes les cards
          </Link>

          {/* option : si tu veux un bouton vers le lien externe */}
          {c.link ? (
            <a className="pill" href={c.link} target="_blank" rel="noreferrer">
              Ouvrir le lien →
            </a>
          ) : null}
        </div>

        <section className="frame">
          <div className="frameBody">
            <div className="kicker">Article card</div>

            <h1 className="titleXL" style={{ marginTop: 10 }}>
              {c.title}
            </h1>

            {(dateLabel || c.author) && (
              <div className="meta">
                {dateLabel ? `Publié le ${new Date(dateLabel).toLocaleString("fr-FR")}` : ""}
                {dateLabel && c.author ? " • " : ""}
                {c.author ? c.author : ""}
              </div>
            )}

            {/* IMAGE */}
            {mediaUrl && (
              <div style={{ marginTop: 16, display: "grid", gap: 8 }}>
                <figure style={{ margin: 0 }}>
                  <div style={{ width: "100%", display: "flex", justifyContent: "center" }}>
                    <img
                      src={mediaUrl}
                      alt={c.title}
                      style={{
                        width: "100%",
                        maxWidth: 760,
                        maxHeight: 360,
                        objectFit: "cover",
                        borderRadius: 10,
                        border: "1px solid var(--line)",
                        display: "block",
                      }}
                    />
                  </div>
                </figure>
              </div>
            )}

            {/* EXCERPT (HTML ou texte) */}
            {c.excerpt ? (
              <div
                style={{ marginTop: 16, color: "var(--muted)", lineHeight: 1.7 }}
                // si excerpt est du texte simple, remplace par: {c.excerpt}
                dangerouslySetInnerHTML={{ __html: c.excerpt }}
              />
            ) : (
              <div style={{ marginTop: 16, color: "var(--muted)" }}>
                Aucun extrait disponible.
              </div>
            )}
          </div>
        </section>
      </div>
    </div>
  );
}
