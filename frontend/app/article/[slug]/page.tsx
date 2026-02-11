import Link from "next/link";
import { apiGet } from "../../lib/api";
import LikeButton from "../../components/LikeButton";


type Media = {
  type: "image" | "video" | "embed";
  url: string | null;        // liens externes
  fileName: string | null;   // upload Vich
  caption?: string | null;
  isMain: boolean;
};

type ArticleItem = {
  id: number;
  title: string;
  slug: string;
  excerpt: string;          // HTML possible
  content: string | null;   // HTML possible
  status: string;
  publishedAt: string | null;
  createdAt: string | null;
  updatedAt: string | null;
  media?: Media[];
};

function toEmbedUrl(raw: string) {
  try {
    if (raw.includes("youtube.com/watch")) {
      const u = new URL(raw);
      const id = u.searchParams.get("v");
      return id ? `https://www.youtube.com/embed/${id}` : raw;
    }
    if (raw.includes("youtu.be/")) {
      const id = raw.split("youtu.be/")[1]?.split("?")[0];
      return id ? `https://www.youtube.com/embed/${id}` : raw;
    }
  } catch {}
  return raw;
}

export default async function ArticlePage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;

  const data = await apiGet<{ item: ArticleItem }>(`/articles/${slug}`);
  const a = data.item;

  const dateLabel = a.publishedAt ?? a.createdAt;

  const mainMedia = a.media?.find((m) => m.isMain) ?? a.media?.[0] ?? null;

  // ✅ URL relative (même domaine nginx)
  const mediaUrl =
    mainMedia?.fileName ? `/uploads/media/${mainMedia.fileName}` : mainMedia?.url ?? null;

  const embedUrl =
    mainMedia?.type === "embed" && mediaUrl ? toEmbedUrl(mediaUrl) : null;

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 40 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>
           <LikeButton slug={slug} />

        </div>

        <section className="frame">
          <div className="frameBody">
            <div className="kicker">Actualités</div>

            <h1 className="titleXL" style={{ marginTop: 10 }}>
              {a.title}
            </h1>

            {dateLabel && (
              <div className="meta">
                Publié le {new Date(dateLabel).toLocaleString("fr-FR")}
              </div>
            )}

            {/* MEDIA (image / video / embed) */}
            {(mediaUrl || embedUrl) && (
              <div
                style={{
                  marginTop: 16,
                  display: "grid",
                  gap: 8,
                }}
              >
                {/* Image */}
                {mediaUrl && mainMedia?.type === "image" && (
                  <figure style={{ margin: 0 }}>
                    <div
                      style={{
                        width: "100%",
                        display: "flex",
                        justifyContent: "center",
                      }}
                    >
                      <img
                      
                        src={mediaUrl}
                        alt={mainMedia.caption ?? a.title}
                        style={{
                          width: "100%",
                          maxWidth: 760,      // ✅ plus petit que la frame
                          maxHeight: 360,     // ✅ moins haut
                          objectFit: "cover",
                          borderRadius: 10,
                          border: "1px solid var(--line)",
                          display: "block",
                        }}
                      />
                    </div>

                    {mainMedia.caption && (
                      <figcaption
                        style={{
                          marginTop: 6,
                          fontSize: 12,
                          color: "var(--muted)",
                          textAlign: "center",
                        }}
                      >
                        {mainMedia.caption}
                      </figcaption>
                    )}
                  </figure>
                )}

                {/* Video fichier */}
                {mediaUrl && mainMedia?.type === "video" && (
                  <figure style={{ margin: 0 }}>
                    <div style={{ width: "100%", display: "flex", justifyContent: "center" }}>
                      <video
                        src={mediaUrl}
                        controls
                        playsInline
                        style={{
                          width: "100%",
                          maxWidth: 760,
                          borderRadius: 10,
                          border: "1px solid var(--line)",
                          display: "block",
                        }}
                      />
                    </div>

                    {mainMedia.caption && (
                      <figcaption
                        style={{
                          marginTop: 6,
                          fontSize: 12,
                          color: "var(--muted)",
                          textAlign: "center",
                        }}
                      >
                        {mainMedia.caption}
                      </figcaption>
                    )}
                  </figure>
                )}

                {/* Embed */}
                {embedUrl && (
                  <figure style={{ margin: 0 }}>
                    <div style={{ width: "100%", display: "flex", justifyContent: "center" }}>
                      <div
                        style={{
                          position: "relative",
                          width: "100%",
                          maxWidth: 760,       // ✅ plus petit
                          paddingTop: "56.25%",
                          borderRadius: 10,
                          overflow: "hidden",
                          border: "1px solid var(--line)",
                          background: "rgba(255,255,255,.03)",
                        }}
                      >
                        <iframe
                          src={embedUrl}
                          title={mainMedia?.caption ?? a.title}
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowFullScreen
                          style={{
                            position: "absolute",
                            inset: 0,
                            width: "100%",
                            height: "100%",
                            border: 0,
                          }}
                        />
                      </div>
                    </div>

                    {mainMedia?.caption && (
                      <figcaption
                        style={{
                          marginTop: 6,
                          fontSize: 12,
                          color: "var(--muted)",
                          textAlign: "center",
                        }}
                      >
                        {mainMedia.caption}
                      </figcaption>
                    )}
                  </figure>
                )}
              </div>
            )}

            {/* ✅ excerpt HTML */}
            <div
              style={{ marginTop: 16, color: "var(--muted)", lineHeight: 1.7 }}
              dangerouslySetInnerHTML={{ __html: a.excerpt }}
            />

            {/* ✅ content HTML */}
            <div
              style={{
                marginTop: 18,
                borderTop: "1px solid var(--line)",
                paddingTop: 18,
                lineHeight: 1.9,
              }}
              dangerouslySetInnerHTML={{ __html: a.content ?? "" }}
            />
          </div>
        </section>
      </div>
    </div>
  );
}
