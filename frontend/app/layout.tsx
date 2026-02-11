import "./globals.css";
import Link from "next/link";
import { apiGet } from "./lib/api";
import { cookies } from "next/headers";
import LogoutButton from "./components/LogoutButton";

function normalizeSections(payload: any): { name: string; slug: string }[] {
  const arr = Array.isArray(payload) ? payload : payload?.items;
  if (!Array.isArray(arr)) return [];

  return arr
    .map((s: any) => ({
      name: typeof s?.name === "string" ? s.name : "",
      slug: typeof s?.slug === "string" ? s.slug : "",
    }))
    .filter((s) => s.name.trim() !== "" && s.slug.trim() !== "" && s.slug !== "undefined");
}

type FooterLinkApiItem = {
  id?: number | null;
  label?: string | null;
  url?: string | null;
  position?: number | null;
  groupName?: string | null;
  isActive?: boolean | null;
};

type FooterLink = {
  id: number;
  label: string;
  url: string;
  position: number;
  groupName: string;
  isActive: boolean; // on normalise en boolean
};

function normalizeFooterLinks(payload: any): FooterLink[] {
  const arr = Array.isArray(payload) ? payload : payload?.items;
  if (!Array.isArray(arr)) return [];

  return arr
    .map((l: FooterLinkApiItem): FooterLink => ({
      id: typeof l?.id === "number" ? l.id : 0,
      label: typeof l?.label === "string" ? l.label.trim() : "",
      url: typeof l?.url === "string" ? l.url.trim() : "",
      position: typeof l?.position === "number" ? l.position : 0,
      groupName: typeof l?.groupName === "string" ? l.groupName.trim() : "",
      // si null, on considère false (tu peux mettre true si tu veux afficher même sans isActive)
      isActive: l?.isActive === true,
    }))
    .filter((l) => l.id > 0 && l.label !== "" && l.url !== "" && l.groupName !== "")
    .sort((a, b) =>
      a.groupName === b.groupName ? a.position - b.position : a.groupName.localeCompare(b.groupName)
    );
}

function isInternalUrl(url: string) {
  return url.startsWith("/") && !url.startsWith("//");
}

export default async function RootLayout({ children }: { children: React.ReactNode }) {
  const store = await cookies();
  const isLoggedIn = !!store.get("auth_token");

  // sections (inchangé)
  let sections: { name: string; slug: string }[] = [];
  try {
    const data = await apiGet<any>("/sections");
    sections = normalizeSections(data);
  } catch {
    sections = [];
  }

  const topNav = sections.slice(0, 3);
  const footerNav = sections.slice(0, 6);

  // footer-links (on récupère tout, puis on filtre ici)
  let footerLinks: FooterLink[] = [];
  try {
    const data = await apiGet<any>("/footer-links");
    footerLinks = normalizeFooterLinks(data);
  } catch {
    footerLinks = [];
  }

  // FILTRAGE : on garde seulement actifs (si chez toi beaucoup sont null → ils ne s'afficheront pas)
  // Si tu veux afficher même quand isActive est null, remplace par: l.isActive !== false
  const activeFooterLinks = footerLinks.filter((l) => l.isActive === true);

  const socialLinks = activeFooterLinks.filter((l) => l.groupName === "social");
  const legalLinks = activeFooterLinks.filter((l) => l.groupName === "legal");

  return (
    <html lang="fr">
      <head>
        <link
          rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        />
      </head>
      <body>
        <div className="topbar">
          <div className="container">
            <div className="navMain">
              <Link href="/" className="brand">
                <span className="brandTitle">AFRICA FACTS PRESS</span>
                <span className="brandTag"> Afrique & diaspora</span>
              </Link>

              <nav className="navLinks" aria-label="Navigation principale">
                <Link href="/">Accueil</Link>

                {topNav.map((s) => (
                  <Link key={s.slug} href={`/section/${encodeURIComponent(s.slug)}`}>
                    {s.name}
                  </Link>
                ))}
              </nav>

              <div className="actions">
                {isLoggedIn ? (
                  <LogoutButton />
                ) : (
                  <>
                    <Link className="btn" href="/connexion">
                      Se connecter
                    </Link>
                    <Link className="btn btnPrimary" href="/abonnement">
                      S’abonner
                    </Link>
                  </>
                )}
              </div>
            </div>

            {sections.length > 0 && (
              <div className="sectionBar">
                <div className="sectionBarInner">
                  {sections.map((s) => (
                    <Link key={s.slug} className="pill" href={`/section/${encodeURIComponent(s.slug)}`}>
                      {s.name}
                    </Link>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>

        {children}

        <footer className="footer">
          <div className="container">
            <div className="footerGrid">
              <div>
                <h4>AFRICA FACTS PRESS</h4>
                <div>Actu, enquêtes, analyses — Afrique & diaspora.</div>
                <div
                  style={{
                    marginTop: 10,
                    display: "flex",
                    gap: 12,
                    flexWrap: "wrap",
                    alignItems: "center",
                    color: "var(--muted)",
                    fontSize: 13,
                  }}
                ></div>
                <Link
                  href="/newsletter"
                  style={{
                    textDecoration: "none",
                    color: "inherit",
                    fontWeight: 700,
                  }}
                >
                  Newsletter
                </Link>
                {/* ✅ Liens légaux */}
                <div
                  style={{
                    marginTop: 10,
                    display: "flex",
                    gap: 12,
                    flexWrap: "wrap",
                    alignItems: "center",
                    color: "var(--muted)",
                    fontSize: 13,
                  }}
                >
                  <Link
                    href="/mentions-legales"
                    style={{
                      textDecoration: "none",
                      color: "inherit",
                      fontWeight: 700,
                    }}
                  >
                    Mentions légales
                  </Link>

                  <span style={{ opacity: 0.5 }}>•</span>

                  <Link
                    href="/conditions-abonnement"
                    style={{
                      textDecoration: "none",
                      color: "inherit",
                      fontWeight: 700,
                    }}
                  >
                    Conditions d’abonnement
                  </Link>
                </div>
              </div>
              <div>
                <h4>Rubriques</h4>
                <div style={{ display: "grid", gap: 8 }}>
                  {footerNav.map((s) => (
                    <Link key={s.slug} href={`/section/${encodeURIComponent(s.slug)}`}>
                      {s.name}
                    </Link>
                  ))}
                </div>
              </div>

              <div>
                <h4>Suivez-nous</h4>
                <div style={{ display: "grid", gap: 8 }}>
                  {socialLinks.map((l) =>
                    isInternalUrl(l.url) ? (
                      <Link key={l.id} href={l.url}>
                        {l.label}
                      </Link>
                    ) : (
                      <a key={l.id} href={l.url} target="_blank" rel="noreferrer">
                        {l.label}
                      </a>
                    )
                  )}
                </div>
              </div>
            </div>

            <div className="small" style={{ display: "flex", gap: 10, flexWrap: "wrap" }}>
              <span>© {new Date().getFullYear()} Wolfram et hart</span>

              {legalLinks.length > 0 && (
                <>
                  <span>—</span>
                  {legalLinks.map((l, idx) => (
                    <span key={l.id} style={{ display: "inline-flex", gap: 10 }}>
                      {isInternalUrl(l.url) ? (
                        <Link href={l.url}>{l.label}</Link>
                      ) : (
                        <a href={l.url} target="_blank" rel="noreferrer">
                          {l.label}
                        </a>
                      )}
                      {idx !== legalLinks.length - 1 && <span>•</span>}
                    </span>
                  ))}
                </>
              )}
            </div>
          </div>
        </footer>
      </body>
    </html>
  );
}
