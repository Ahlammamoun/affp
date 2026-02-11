
import DestinationImage from "./components/DestImageRotator";
import Link from "next/link";
import { apiGet } from "./lib/api";
import LiveTicker from "./components/LiveTicker";
import AdCarousel from "./components/AdCarousel";



type Article = { id: number; title: string; slug: string };

type MustReadItem = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;      // HTML possible
  publishedAt: string | null;
  thumb: string | null;        // "/uploads/media/xxx.jpg" ou URL externe
};

type DossierListItem = {
  id: number;
  title: string;
  slug: string;
  thumb: string | null;
  publishedAt: string | null;
  updatedAt: string | null;
  // selon ton repo : peut s’appeler articlesCount ou articles_count ou articles.length
  articlesCount?: number | null;
  author?: { name?: string | null } | null;
};
type FeaturedResp = {
  item: {
    key: string;
    article: {
      id: number;
      title: string;
      slug: string;
      excerpt: string | null;
      thumb: string | null;
      publishedAt: string | null;
      createdAt: string;
      updatedAt: string | null;
      dateLabel?: string | null;
    } | null;
  } | null;
};
type AdItem = { badge: string; title: string; text: string; href: string };


type LiveItem = { id: number; time: string; tag: string; title: string; href: string | null };

type EventItem = {
  id: number;
  title: string;
  slug: string;
  category: string | null;
  city: string | null;
  country: string | null;
  eventAt: string | null;
  thumb: string | null;
  link: string | null;
  description: string | null;
};

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

type DestinationItem = {
  id: number;
  title: string;
  slug: string;
  city: string | null;
  country: string | null;
  publishedAt: string | null;
  thumb: string | null;
  excerpt: string | null;
};

type WeeklyDest = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  city: string | null;
  country: string | null;
  link: string | null;

  publishedAt: string | null;
  thumb: string | null;
  media?: {
    id: number;
    fileName: string | null;
    url: string | null;
    isMain: boolean;
  }[];
};


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

const LIVE = [
  { time: "20:37", tag: "Live", title: "Crise au Sahel : les dernières informations" },
  { time: "20:27", tag: "Alerte", title: "CAN : décision importante attendue" },
  { time: "20:14", tag: "", title: "Économie : pression sur les prix des importations" },
  { time: "19:54", tag: "", title: "Diplomatie : sommet annoncé à Addis-Abeba" },
  { time: "19:50", tag: "Alerte", title: "Culture : hommages après une disparition" },
  { time: "19:22", tag: "", title: "Société : tensions autour d’une réforme" },
  { time: "18:58", tag: "", title: "Diaspora : mobilisation en Europe" },
  { time: "18:44", tag: "", title: "Enquête : réseaux et désinformation" },
  { time: "18:20", tag: "", title: "Sport : analyse des dernières rencontres" },
];

type SmallCard = { title: string; text: string; href: string; thumb?: string | null };

const COMM_CARDS: SmallCard[] = [
  { title: "Offre du jour", text: "Réduction limitée – à saisir.", href: "/promo/offre-du-jour", thumb: "/img/comm/offre.jpg" },
  { title: "Services", text: "Découvrez nos partenaires.", href: "/partners", thumb: "/img/comm/services.jpg" },
  { title: "Offre du jour", text: "Réduction limitée – à saisir.", href: "/promo/offre-du-jour", thumb: "/img/comm/offre.jpg" },
  { title: "Services", text: "Découvrez nos partenaires.", href: "/partners", thumb: "/img/comm/services.jpg" },
  { title: "Offre du jour", text: "Réduction limitée – à saisir.", href: "/promo/offre-du-jour", thumb: "/img/comm/offre.jpg" },
  { title: "Services", text: "Découvrez nos partenaires.", href: "/partners", thumb: "/img/comm/services.jpg" },
  { title: "Offre du jour", text: "Réduction limitée – à saisir.", href: "/promo/offre-du-jour", thumb: "/img/comm/offre.jpg" },
  { title: "Services", text: "Découvrez nos partenaires.", href: "/partners", thumb: "/img/comm/services.jpg" },
  { title: "Offre du jour", text: "Réduction limitée – à saisir.", href: "/promo/offre-du-jour", thumb: "/img/comm/offre.jpg" },
  { title: "Services", text: "Découvrez nos partenaires.", href: "/partners", thumb: "/img/comm/services.jpg" },
];


export default async function HomePage() {
  // ✅ liste articles (GET /api/articles)
  const data = await apiGet<{ items: Article[] }>("/articles");
  const items = data.items ?? [];

  // ✅ dossiers (GET /api/dossiers)
  const dossiersData = await apiGet<{ items?: DossierListItem[] }>("/dossiers?limit=8&status=published&order=desc");
  const dossiers = dossiersData.items ?? [];

  // ✅ must-read (GET /api/articles/must-read)
  // si ton backend bug, on fallback à null au lieu de casser toute la page
  let must: MustReadItem | null = null;
  try {
    const mustRead = await apiGet<{ item: MustReadItem | null }>("/articles/must-read");
    must = mustRead.item;
  } catch {
    must = null;
  }

  // ✅ PORTRAIT DU JOUR (GET /api/featured/portrait_du_jour)
  let portrait: FeaturedResp["item"] = null;
  try {
    const featured = await apiGet<FeaturedResp>("/featured/portrait_du_jour");
    portrait = featured.item;
  } catch {
    portrait = null;
  }


  // ✅ live ticker (GET /api/live)
  let liveItems: LiveItem[] = [];
  try {
    const live = await apiGet<{ items: LiveItem[] }>("/live?limit=20");
    liveItems = live.items ?? [];
  } catch {
    liveItems = LIVE.map((x, i) => ({ id: i + 1, time: x.time, tag: x.tag, title: x.title, href: null }));
  }

  // ✅ zone commerciale (GET /api/ads)
  let ads: AdItem[] = [];
  try {
    const adResp = await apiGet<{ items: AdItem[] }>("/ads");
    ads = adResp.items ?? [];
  } catch {
    ads = [];
  }


  // ✅ events (GET /api/events/upcoming)
  let events: EventItem[] = [];
  try {
    const ev = await apiGet<{ items: EventItem[] }>("/events/upcoming?limit=6");
    events = ev.items ?? [];
  } catch {
    events = [];
  }

  // ✅ article cards (GET /api/article-cards)
  let articleCards: ArticleCardItem[] = [];
  try {
    const r = await apiGet<{ items: ArticleCardItem[] }>("/article-cards?limit=12");
    articleCards = r.items ?? [];
  } catch {
    articleCards = [];
  }

  // api destination 
  let weeklyDest: WeeklyDest | null = null;
  try {
    const r = await apiGet<{ item: WeeklyDest | null }>("/destinations/weekly");
    weeklyDest = r.item ?? null;
  } catch {
    weeklyDest = null;
  }


  // ✅ premium briefs (GET /api/premium-briefs)
  let premiumBrief: PremiumBriefListItem | null = null;
  try {
    const r = await apiGet<{ items: PremiumBriefListItem[] }>("/premium-briefs?limit=1");
    premiumBrief = r.items?.[0] ?? null;
  } catch {
    premiumBrief = null;
  }

  const portraitArticle = portrait?.article ?? null;

  const lead = items[0];
  const selection = items.slice(1, 7);
  const centre = items.slice(0, 2);

  const rightList = items.slice(0, 6);
  const more = items.slice(19);

  function stripHtml(html?: string | null) {
    if (!html) return "";
    return html.replace(/<[^>]*>/g, "").replace(/&nbsp;/g, " ").trim();
  }

  return (
    <div className="container">
      <div className="pageGrid">
        {/* COLONNE GAUCHE */}
        <div className="stack">
          <section
            className="frame"

          >
            <div className="frameHead">
              <div className="frameTitle">À la une</div>
              <Link href={must ? `/article/${must.slug}` : "/articles"}>Voir plus</Link>
            </div>

            <div
              className="frameBody"
              style={{
                padding: 20
              }}
            >
              {must ? (
                <>
                  {/* Top badges */}
                  <div
                    style={{
                      display: "flex",
                      alignItems: "center",
                      gap: 10,
                      marginBottom: 12
                    }}
                  >
                    <div className="kicker">A la une</div>


                  </div>

                  {/* Title */}
                  <h2
                    style={{
                      fontSize: 25,
                      lineHeight: 1.25,
                      fontWeight: 800,
                      margin: "10px 0"
                    }}
                  >
                    <Link
                      href={`/article/${must.slug}`}
                      style={{
                        textDecoration: "none"
                      }}
                    >
                      {must.title}
                    </Link>
                  </h2>

                  {/* Excerpt texte pur */}
                  {must.excerpt && (
                    <p
                      style={{
                        marginTop: 12,
                        fontSize: 16,
                        lineHeight: 1.7,
                        color: "var(--muted)",
                        maxWidth: "60ch"
                      }}
                    >
                      {stripHtml(must.excerpt)}
                    </p>
                  )}

                  {/* Meta */}
                  <div
                    className="meta"
                    style={{
                      marginTop: 14,
                      fontSize: 13,
                      opacity: 0.8
                    }}
                  >
                    Édition du jour • Analyses & reportages
                  </div>
                </>
              ) : (
                <div style={{ color: "var(--muted)" }}>
                  Ajoute des articles dans l’admin.
                </div>
              )}
            </div>
          </section>



          <section className="frame">
            <div className="frameHead">
              <div className="frameTitle">Sélection</div>

            </div>
            <div className="frameBody">
              <div className="list">
                {selection.map((a) => (
                  <div className="listItem" key={a.id}>
                    <Link href={`/article/${a.slug}`}>{a.title}</Link>
                    <span className="smallMuted">Analyse</span>
                  </div>
                ))}
              </div>
            </div>
          </section>

          {/* Scroll horizontal “dossiers” */}
          {/* Scroll horizontal “dossiers” (API /dossiers) */}
          <section className="frame">
            <div className="frameHead">
              <div className="frameTitle">Dossiers</div>
              <Link className="frameLink" href="/dossiers">Voir</Link>
            </div>

            <div className="frameBody">
              <div className="hScroll">
                {(dossiers.length ? dossiers : []).map((d) => {
                  const dt = d.updatedAt ?? d.publishedAt;
                  const dateLabel = dt ? new Date(dt).toLocaleDateString("fr-FR") : null;

                  // nb d’articles : on tente articlesCount sinon "—"
                  const count =
                    typeof d.articlesCount === "number"
                      ? d.articlesCount
                      : null;

                  const authorName = d.author?.name ?? null;

                  return (
                    <div className="hCard" key={d.id}>
                      <div className="hCardInner">
                        <div className="kicker">
                          Dossier
                          {authorName ? ` • ${authorName}` : ""}
                          {dateLabel ? ` • MAJ ${dateLabel}` : ""}
                        </div>

                        <div style={{ marginTop: 8, fontWeight: 900, lineHeight: 1.25 }}>
                          <Link href={`/dossier/${d.slug}`}>{d.title}</Link>
                        </div>

                        {/* ligne “vivante” : nb articles + CTA */}
                        <div style={{ marginTop: 8, color: "var(--muted)" }}>
                          {count !== null ? `${count} articles` : "Série complète"} •{" "}
                          <Link href={`/dossier/${d.slug}`}>→</Link>
                        </div>
                      </div>
                    </div>
                  );
                })}

                {dossiers.length === 0 && (
                  <div style={{ color: "var(--muted)" }}>
                    Aucun dossier publié pour le moment.
                  </div>
                )}
              </div>
            </div>
          </section>

        </div>

        {/* COLONNE CENTRE */}
        <div className="stack">
          <section className="frame">
            <div className="frameHead">
              <div className="frameTitle">À ne pas manquer</div>
              {/* <Link className="frameLink" href="/section/selection">Plus</Link> */}
            </div>

            <div className="frameBody">
              {/* ✅ MUST READ (1 grosse carte) */}
              {must ? (
                <article className="frame" style={{ marginBottom: 14 }}>
                  <div className="cardPad">
                    <div className="kicker">À ne pas manquer</div>

                    {must.thumb && (
                      <div style={{ marginTop: 10 }}>
                        <Link href={`/article/${must.slug}`}>
                          <img
                            src={must.thumb}
                            alt={must.title}
                            loading="lazy"
                            style={{
                              width: "100%",
                              height: 280,
                              objectFit: "cover",
                              borderRadius: 10,
                              border: "1px solid var(--line)",
                              display: "block",
                            }}
                          />
                        </Link>
                      </div>
                    )}

                    <div className="cardTitle" style={{ marginTop: 12 }}>
                      <Link href={`/article/${must.slug}`}>{must.title}</Link>
                    </div>

                    <div
                      className="mustExcerpt"
                      style={{ marginTop: 8, color: "var(--muted)", lineHeight: 1.7 }}
                      dangerouslySetInnerHTML={{
                        __html: must.excerpt ?? "<div>Clique pour lire la suite.</div>",
                      }}
                    />

                    {must.publishedAt && (
                      <div className="meta" style={{ marginTop: 10 }}>
                        Publié le {new Date(must.publishedAt).toLocaleString("fr-FR")}
                      </div>
                    )}
                  </div>
                </article>
              ) : null}

              {/* ✅ le reste (tes cartes existantes) */}
              <div className="cards">
                {centre.map((a) => (
                  <article key={a.id} className="frame">
                    <div className="cardPad">
                      <div className="kicker">Décryptage</div>
                      <div className="cardTitle">
                        <Link href={`/article/${a.slug}`}>{a.title}</Link>
                      </div>
                      <div className="cardDesc">
                        Clique pour lire. (On ajoutera image, auteur, date, extrait.)
                      </div>
                    </div>
                  </article>
                ))}
              </div>
            </div>
          </section>

          {/* ✅ NOTRE PORTRAIT DU JOUR */}
          {portraitArticle ? (
            <section className="frame">
              <div className="frameHead">
                <div className="frameTitle">Notre portrait du jour</div>
                <Link className="frameLink" href={`/article/${portraitArticle.slug}`}>
                  Lire →
                </Link>
              </div>

              <div className="frameBody">
                {portraitArticle.thumb ? (
                  <div>
                    <Link href={`/article/${portraitArticle.slug}`}>
                      <img
                        src={portraitArticle.thumb}
                        alt={portraitArticle.title}
                        loading="lazy"
                        style={{
                          width: "100%",
                          height: 200,
                          objectFit: "cover",          // ✅ comme avant
                          objectPosition: "15% 25%",// ✅ moins de têtes coupées
                          borderRadius: 10,
                          border: "1px solid var(--line)",
                          display: "block",
                        }}
                      />
                    </Link>
                  </div>
                ) : null}

                <div style={{ marginTop: 10, fontWeight: 900, lineHeight: 1.25 }}>
                  <Link href={`/article/${portraitArticle.slug}`}>
                    {portraitArticle.title}
                  </Link>
                </div>

                {portraitArticle.excerpt ? (
                  <div
                    className="mustExcerpt"
                    style={{ marginTop: 8, color: "var(--muted)", lineHeight: 1.6 }}
                    dangerouslySetInnerHTML={{ __html: portraitArticle.excerpt }}
                  />
                ) : (
                  <div style={{ marginTop: 8, color: "var(--muted)" }}>
                    Clique pour lire le portrait.
                  </div>
                )}

                {(portraitArticle.publishedAt || portraitArticle.createdAt) && (
                  <div className="meta" style={{ marginTop: 10 }}>
                    Publié le{" "}
                    {(() => {
                      const dt = portraitArticle.publishedAt ?? portraitArticle.createdAt;
                      return dt ? new Date(dt).toLocaleDateString("fr-FR") : "";
                    })()}

                  </div>
                )}
              </div>
            </section>
          ) : null}

          {/* ⭐ RÉSUMÉ PREMIUM (API) */}
          <section className="frame" style={{ borderColor: "var(--accent)" }}>
            <div className="frameHead">
              <div className="frameTitle">⭐ Résumé premium – L’essentiel de l’actualité africaine</div>

              <Link className="frameLink" href={premiumBrief ? `/premium/${premiumBrief.slug}` : "/premium"}>
                Accès abonné →
              </Link>
            </div>

            <div className="frameBody">
              <div style={{ display: "grid", gap: 12 }}>
                <div
                  style={{
                    fontSize: "0.75rem",
                    letterSpacing: "0.08em",
                    textTransform: "uppercase",
                    color: "var(--accent)",
                    fontWeight: 700,
                  }}
                >
                  {premiumBrief?.scopeLabel || premiumBrief?.scope
                    ? `Analyse premium • ${premiumBrief.scopeLabel || premiumBrief.scope}`
                    : "Analyse réservée aux abonnés"}
                  {premiumBrief?.publishedAt
                    ? ` • ${new Date(premiumBrief.publishedAt).toLocaleString("fr-FR")}`
                    : ""}
                </div>

                <h3 style={{ margin: 0, fontSize: "1.25rem", fontWeight: 800 }}>
                  <Link
                    href={premiumBrief ? `/premium/${premiumBrief.slug}` : "/premium"}
                    style={{ color: "inherit", textDecoration: "none" }}
                  >
                    {premiumBrief?.title ?? "Afrique – Les faits clés à comprendre aujourd’hui"}
                  </Link>
                </h3>

                <p style={{ margin: 0, color: "var(--muted)", lineHeight: 1.6 }}>
                  Une synthèse claire et contextualisée des événements majeurs :
                  politique, sécurité, économie et enjeux régionaux.
                </p>

                <ul style={{ margin: 0, paddingLeft: 18 }}>
                  {(premiumBrief?.bullets?.filter(Boolean).slice(0, 3) ?? []).length > 0 ? (
                    premiumBrief!.bullets!.filter(Boolean).slice(0, 3).map((b, i) => <li key={i}>{b}</li>)
                  ) : (
                    <>
                      <li>Ce qui s’est réellement passé</li>
                      <li>Pourquoi c’est important maintenant</li>
                      <li>Ce que cela peut changer à court terme</li>
                    </>
                  )}
                </ul>

                <div
                  style={{
                    marginTop: 6,
                    padding: "10px 12px",
                    borderLeft: "3px solid var(--accent)",
                    background: "rgba(0,0,0,0.03)",
                    fontStyle: "italic",
                  }}
                >
                  “Nous expliquons les enjeux derrière les faits, sans bruit ni sensationnalisme.”
                </div>

                <div>
                  <Link className="frameLink" href={premiumBrief ? `/premium/${premiumBrief.slug}` : "/premium"}>
                    Lire le résumé complet →
                  </Link>
                </div>
              </div>
            </div>
          </section>




        </div>

        {/* COLONNE DROITE */}
        <div className="stack">
          <section className="frame">
            <div className="frameHead">
              <div className="frameTitle">Actu en continu</div>
              <Link className="frameLink" href={lead ? `/article/${lead.slug}` : "/articles"}>
                Voir plus
              </Link>

            </div>

            <div className="frameBody">
              <LiveTicker
                items={liveItems.map((x) => ({
                  time: x.time,
                  tag: x.tag,
                  title: x.title,
                  href: x.href, // nouveau
                }))}

                pageSize={60}
              />
            </div>
          </section>

          <section className="frame">
            <div className="frameHead">
              <div className="frameTitle">Les plus lus</div>
              <Link className="frameLink" href="/section/populaire">Classement</Link>
            </div>
            <div className="frameBody">
              <ol style={{ margin: 0, paddingLeft: 18, display: "grid", gap: 10 }}>
                {rightList.map((a) => (
                  <li key={a.id}>
                    <Link href={`/article/${a.slug}`}>{a.title}</Link>
                  </li>
                ))}
              </ol>
            </div>
          </section>
        </div>
      </div>



      <section className="frame">
        <div className="frameHead">
          <div className="frameTitle">Événements à venir</div>
        </div>

        <div className="frameBody">
          {events.length > 0 ? (
            <div className="cards">
              {events.map((ev) => {
                const dateLabel = ev.eventAt
                  ? new Date(ev.eventAt).toLocaleString("fr-FR", {
                    weekday: "short",
                    day: "2-digit",
                    month: "short",
                    hour: "2-digit",
                    minute: "2-digit",
                  })
                  : null;

                const place = [ev.city, ev.country].filter(Boolean).join(" • ");
                const href = `/event/${ev.slug}`;

                return (
                  <article key={ev.id} className="frame">
                    <div
                      className="cardPad"
                      style={{
                        display: "flex",
                        gap: 16,
                        alignItems: "stretch",
                      }}
                    >
                      {ev.thumb ? (
                        <a
                          href={href}
                          target={ev.link ? "_blank" : undefined}
                          rel="noreferrer"
                          style={{
                            flex: "0 0 50%",
                            maxWidth: "53%",
                            height: 250,
                            borderRadius: 10,
                            overflow: "hidden",
                            border: "1px solid var(--line)",
                            background: "#00000008",
                          }}
                        >
                          <img
                            src={ev.thumb}
                            alt={ev.title}
                            loading="lazy"
                            style={{
                              width: "100%",
                              height: "100%",
                              objectFit: "cover", // pas coupé
                              objectPosition: "50% 15%",
                              display: "block",
                            }}
                          />
                        </a>
                      ) : (
                        <div
                          style={{
                            flex: "0 0 40%",
                            maxWidth: "40%",
                            borderRadius: 10,
                            border: "1px dashed var(--line)",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            color: "var(--muted)",
                          }}
                        >
                          Image à venir
                        </div>
                      )}

                      {/* TEXTE */}
                      <div style={{ flex: 1 }}>
                        <div className="kicker">
                          {ev.category || "Événement"}
                          {dateLabel ? ` • ${dateLabel}` : ""}
                        </div>

                        <div className="cardTitle" style={{ marginTop: 6 }}>
                          <a href={href} target={ev.link ? "_blank" : undefined}>
                            {ev.title}
                          </a>
                        </div>

                        {place && (
                          <div className="meta" style={{ marginTop: 6 }}>
                            {place}
                          </div>
                        )}

                        {ev.link ? (
                          <a
                            href={ev.link}
                            target="_blank"
                            rel="noreferrer"
                            style={{
                              display: "inline-block",
                              marginTop: 10,
                              padding: "6px 12px",
                              fontSize: 13,
                              fontWeight: 600,
                            
                              border: "1px solid var(--line)",
                              textDecoration: "none",
                              color: "var(--text)",
                              background: "rgba(255,255,255,0.04)",
                              transition: "all .15s ease"
                            }}
                          >
                            Site de l'événement →
                          </a>
                        ) : null}

                      </div>
                    </div>
                  </article>

                );
              })}
            </div>
          ) : (
            <div style={{ color: "var(--muted)" }}>
              Aucun événement à venir pour le moment.
            </div>
          )}
        </div>
      </section>



      {/* ZONE COMMERCIALE */}
      <section className="frame zoneCommerciale fullSection">
        <div className="frameHead">
          <div className="frameTitle">Zone commerciale</div>
          <div className="frameLink">Sponsorisé</div>
        </div>

        <div className="frameBody">
          {ads.length > 0 ? (
            <AdCarousel autoPlayMs={4500} items={ads} />
          ) : (
            <div style={{ color: "var(--muted)" }}>
              Aucune campagne active pour le moment.
            </div>
          )}
        </div>


      </section>

      {/* MINI CARTES  (libres, même largeur que les frames) */}
      <div className="commGrid fullSection">
        {articleCards.slice(0, 12).map((c) => (
          <Link key={c.id} href={`/article-card/${c.slug}`} className="commCard">
            {c.thumb ? (
              <img className="commImg" src={c.thumb} alt={c.title} loading="lazy" />
            ) : (
              <div className="commImgEmpty">Image</div>
            )}

            <div className="adTitle">{c.title}</div>
            {/* {c.excerpt ? <div className="adText">{c.excerpt}</div> : null} */}
            <div className="adCta">Découvrir →</div>
          </Link>
        ))}
      </div>


      {weeklyDest ? (
        <section className="frame" style={{ marginBottom: 30 }}>

          <div className="frameHead">
            <div className="frameTitle">Destination de la semaine</div>
            <Link className="frameLink" href="/destination">Voir toutes</Link>
          </div>

          <div className="frameBody">
            <article className="frame">
              <div
                className="cardPad"
                style={{
                  display: "flex",
                  gap: 16,
                  alignItems: "stretch",
                }}
              >
                {/* TEXTE À GAUCHE */}
                <div style={{ flex: 1, minWidth: 0 }}>
                  {/* Badge / kicker */}
                  <div
                    style={{
                      display: "inline-flex",
                      alignItems: "center",
                      gap: 8,
                      padding: "6px 10px",

                      background: "rgba(0,0,0,.06)",
                      border: "1px solid var(--line)",
                      color: "var(--accent)",
                      fontWeight: 800,
                      letterSpacing: "0.08em",
                      textTransform: "uppercase",
                      fontSize: 12,
                    }}
                  >
                    <span aria-hidden>✈️</span> Destination de la semaine
                  </div>

                  {/* Titre */}
                  <h2
                    style={{
                      marginTop: 12,
                      fontSize: "2rem",
                      lineHeight: 1.1,
                      fontWeight: 950,
                      letterSpacing: "-0.03em",
                    }}
                  >
                    <Link href={`/destination/${weeklyDest.slug}`} style={{ textDecoration: "none" }}>
                      {weeklyDest.title}
                    </Link>
                  </h2>

                  {/* Chips infos (lieu + date) */}
                  <div style={{ marginTop: 10, display: "flex", gap: 8, flexWrap: "wrap" }}>
                    {(weeklyDest.city || weeklyDest.country) && (
                      <span
                        style={{
                          fontSize: 12,
                          color: "var(--muted)",
                          border: "1px solid var(--line)",
                          padding: "6px 10px",

                          background: "rgba(255,255,255,.04)",
                        }}
                      >
                        📍 {[weeklyDest.city, weeklyDest.country].filter(Boolean).join(", ")}
                      </span>
                    )}

                    {weeklyDest.publishedAt && (
                      <span
                        suppressHydrationWarning
                        style={{
                          fontSize: 12,
                          color: "var(--muted)",
                          border: "1px solid var(--line)",
                          padding: "6px 10px",

                          background: "rgba(255,255,255,.04)",
                        }}
                      >
                        🗓️ {new Date(weeklyDest.publishedAt).toLocaleDateString("fr-FR")}
                      </span>
                    )}
                  </div>

                  {/* Excerpt */}
                  {weeklyDest.excerpt ? (
                    <div
                      style={{
                        marginTop: 14,
                        color: "var(--muted)",
                        fontSize: 15,
                        lineHeight: 1.75,
                        maxWidth: 520,
                      }}
                      className="destExcerpt"
                      dangerouslySetInnerHTML={{ __html: weeklyDest.excerpt }}
                    />
                  ) : (
                    <div style={{ marginTop: 14, color: "var(--muted)", fontSize: 15, lineHeight: 1.75 }}>
                      Un spot parfait pour s’évader cette semaine.
                    </div>
                  )}

                  {/* CTA */}
                  <div style={{ marginTop: 16, display: "flex", alignItems: "center", gap: 12, flexWrap: "wrap" }}>
                    {weeklyDest.link ? (
                      <a
                        href={weeklyDest.link}
                        target="_blank"
                        rel="noopener noreferrer"
                        style={{
                          display: "inline-flex",
                          alignItems: "center",
                          gap: 10,
                          padding: "10px 16px",

                          background: "var(--accent)",
                          color: "#fff",
                          fontWeight: 800,
                          textDecoration: "none",
                          boxShadow: "0 10px 30px rgba(0,0,0,.18)",
                        }}
                      >
                        Explorer la destination <span aria-hidden>→</span>
                      </a>
                    ) : (
                      <Link
                        href={`/destination/${weeklyDest.slug}`}
                        style={{
                          display: "inline-flex",
                          alignItems: "center",
                          gap: 10,
                          padding: "10px 16px",

                          background: "var(--accent)",
                          color: "#fff",
                          fontWeight: 800,
                          textDecoration: "none",
                          boxShadow: "0 10px 30px rgba(0,0,0,.18)",
                        }}
                      >
                        Explorer la destination <span aria-hidden>→</span>
                      </Link>
                    )}
                  </div>

                </div>

                {/* PHOTO À DROITE */}
                <DestinationImage
                  title={weeklyDest.title}
                  thumb={weeklyDest.thumb}
                  media={weeklyDest.media}
                />




              </div>
            </article>
          </div>
        </section>
      ) : null}


      {/* clamp excerpt */}
      <style>{`
        .mustExcerpt{
          display:-webkit-box;
          -webkit-line-clamp:3;
          -webkit-box-orient:vertical;
          overflow:hidden;
        }
        .mustExcerpt p{ margin:0; }
      `}</style>
    </div>
  );
}
