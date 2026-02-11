
import Link from "next/link";
import { apiGet } from "../lib/api";

type ArticleCardItem = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  thumb: string | null;
  author: string | null;
  publishedAt: string | null;
  link: string | null;
};

export default async function ArticleCardListPage() {
  const data = await apiGet<{ items: ArticleCardItem[] }>("/article-cards?limit=48");
  const items = data.items ?? [];

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 40 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
        </div>

        <section className="frame">
          <div className="frameHead">
            <div className="frameTitle">Toutes les cards</div>
          </div>

          <div className="frameBody">
            {items.length === 0 ? (
              <div style={{ color: "var(--muted)" }}>Aucune card pour le moment.</div>
            ) : (
              <div className="commGrid fullSection">
                {items.map((c) => (
                  <Link key={c.id} href={`/article-card/${c.slug}`} className="commCard">
                    {c.thumb ? (
                      <img className="commImg" src={c.thumb} alt={c.title} loading="lazy" />
                    ) : (
                      <div className="commImgEmpty">Image</div>
                    )}

                    <div className="adTitle">{c.title}</div>

                    {c.excerpt ? <div className="adText">{c.excerpt}</div> : null}

                    <div className="adCta">Ouvrir →</div>
                  </Link>
                ))}
              </div>
            )}
          </div>
        </section>
      </div>

      {/* clamp excerpt comme sur ta home */}
      <style>{`
        .adText{
          display:-webkit-box;
          -webkit-line-clamp:3;
          -webkit-box-orient:vertical;
          overflow:hidden;
        }
      `}</style>
    </div>
  );
}
