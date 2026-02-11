import Link from "next/link";
import { apiGet } from "../../lib/api";

type EventItem = {
  id: number;
  title: string;
  slug: string;
  category: string | null;
  city: string | null;
  country: string | null;
  eventAt: string | null;
  thumb: string | null;
  description: string | null;
  link: string | null;
};

export default async function EventPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;

  const data = await apiGet<{ item: EventItem }>(`/events/${slug}`);
  const ev = data.item;

  const dateLabel = ev.eventAt ?? null;
  const place = [ev.city, ev.country].filter(Boolean).join(" • ");
  const mediaUrl = ev.thumb ?? null;

  return (
    <div className="container" style={{ paddingTop: 22, paddingBottom: 40 }}>
      <div style={{ display: "grid", gap: 14 }}>
        <div style={{ display: "flex", gap: 10, alignItems: "center", flexWrap: "wrap" }}>
          <Link className="pill" href="/">
            ← Accueil
          </Link>

          {ev.link ? (
            <a className="pill" href={ev.link} target="_blank" rel="noreferrer">
              Site de l'événement →
            </a>
          ) : null}
        </div>

        <section className="frame">
          <div className="frameBody">
            <div className="kicker">{ev.category ? ev.category : "Événement"}</div>

            <h1 className="titleXL" style={{ marginTop: 10 }}>
              {ev.title}
            </h1>

            {(dateLabel || place) && (
              <div className="meta">
                {dateLabel ? `Le ${new Date(dateLabel).toLocaleString("fr-FR")}` : ""}
                {dateLabel && place ? " • " : ""}
                {place ? place : ""}
              </div>
            )}

            {/* IMAGE */}
            {mediaUrl && (
              <div style={{ marginTop: 16, display: "grid", gap: 8 }}>
                <figure style={{ margin: 0 }}>
                  <div style={{ width: "100%", display: "flex", justifyContent: "center" }}>
                    <img
                      src={mediaUrl}
                      alt={ev.title}
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

            {/* DESCRIPTION */}
            {ev.description ? (
              <div
                style={{ marginTop: 16, color: "var(--muted)", lineHeight: 1.7 }}
              >
                {ev.description}
              </div>
            ) : (
              <div style={{ marginTop: 16, color: "var(--muted)" }}>
                Aucune description disponible.
              </div>
            )}
          </div>
        </section>
      </div>
    </div>
  );
}
