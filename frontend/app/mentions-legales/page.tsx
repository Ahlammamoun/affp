// app/mentions-legales/page.tsx
import Link from "next/link";

export default function MentionsLegalesPage() {
    return (
        <div className="container">
            <div className="pageGrid">
                <div className="stack" style={{ gridColumn: "1 / -1" }}>
                    <section className="frame">
                        <div className="frameHead">
                            <div className="frameTitle">Mentions légales</div>
                            <Link className="frameLink" href="/">
                                Accueil
                            </Link>
                        </div>

                        <div className="frameBody" style={{ padding: 20 }}>
                            <div
                                className="kicker"
                                style={{
                                    display: "inline-flex",
                                    alignItems: "center",
                                    gap: 8,
                                    padding: "6px 10px",
                                    border: "1px solid var(--line)",
                                    background: "rgba(255,255,255,.04)",
                                    fontWeight: 800,
                                    letterSpacing: "0.08em",
                                    textTransform: "uppercase",
                                    fontSize: 12,
                                }}
                            >
                                📌 Informations légales
                            </div>

                            <h1 style={{ marginTop: 12, fontSize: 28, lineHeight: 1.2, fontWeight: 950 }}>
                                Mentions légales
                            </h1>

                            <div className="meta" style={{ marginTop: 8 }}>
                                Dernière mise à jour :{" "}
                                <span suppressHydrationWarning>{new Date().toLocaleDateString("fr-FR")}</span>
                            </div>

                            <div style={{ marginTop: 16, display: "grid", gap: 14 }}>
                                <Block title="Éditeur du site">
                                    <Line label="Nom / Société" value="Wolfram et Hart" />

                                    <Line label="Adresse" value="200 rue de la Croix Nivert" />
                                    <Line label="Email" value="contact@wfhart.com" />



                                </Block>

                                <Block title="Hébergement">
                                    <Line label="Hébergeur" value="ovh" />
                                    <Line label="Adresse" value="2 rue Kellermann" />

                                    <Line label="Site web" value="https://www.ovh.com" />
                                </Block>

                                <Block title="Propriété intellectuelle">
                                    <p style={pStyle}>
                                        L’ensemble des contenus (textes, images, logos, vidéos, graphismes, mise en page, base de
                                        données) est protégé par le droit d’auteur et/ou le droit des marques. Toute
                                        reproduction, représentation, modification, publication ou adaptation, totale ou
                                        partielle, est interdite sans autorisation écrite préalable, sauf exceptions prévues par
                                        la loi.
                                    </p>
                                </Block>

                                <Block title="Responsabilité">
                                    <p style={pStyle}>
                                        L’éditeur met en œuvre des moyens raisonnables pour assurer l’exactitude et la mise à jour
                                        des informations publiées. Toutefois, des erreurs ou omissions peuvent survenir.
                                        L’utilisateur reconnaît utiliser les informations sous sa responsabilité exclusive.
                                    </p>
                                </Block>

                                <Block title="Liens externes">
                                    <p style={pStyle}>
                                        Le site peut contenir des liens vers des sites tiers. L’éditeur n’exerce aucun contrôle
                                        sur ces sites et décline toute responsabilité quant à leur contenu, produits ou services.
                                    </p>
                                </Block>

                                <Block title="Données personnelles">
                                    <p style={pStyle}>
                                        Pour en savoir plus sur la collecte et le traitement de vos données, consultez la{" "}
                                        <Link href="/politique-de-confidentialite" style={linkStyle}>
                                            Politique de confidentialité
                                        </Link>
                                        .
                                    </p>
                                </Block>

                             
                            </div>

                            <div style={{ marginTop: 18, display: "flex", gap: 10, flexWrap: "wrap" }}>
                                <Link
                                    href="/conditions-abonnement"
                                    style={ctaStyle}
                                >
                                    Conditions d’abonnement <span aria-hidden>→</span>
                                </Link>

                            </div>
                        </div>
                    </section>

                    <style>{`
            .legalGrid {
              display: grid;
              gap: 12px;
            }
          `}</style>
                </div>
            </div>
        </div>
    );
}

function Block({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <div
            style={{
                border: "1px solid var(--line)",

                padding: 14,
                background: "rgba(255,255,255,.02)",
            }}
        >
            <div style={{ fontWeight: 900, marginBottom: 8 }}>{title}</div>
            {children}
        </div>
    );
}

function Line({ label, value }: { label: string; value: string }) {
    return (
        <div style={{ display: "flex", gap: 10, alignItems: "baseline", marginTop: 6, flexWrap: "wrap" }}>
            <div style={{ minWidth: 210, color: "var(--muted)", fontSize: 13 }}>{label}</div>
            <div style={{ fontWeight: 650 }}>{value}</div>
        </div>
    );
}

const pStyle: React.CSSProperties = {
    margin: 0,
    lineHeight: 1.75,
    color: "var(--muted)",
};

const linkStyle: React.CSSProperties = {
    textDecoration: "none",
    fontWeight: 800,
};

const ctaStyle: React.CSSProperties = {
    display: "inline-flex",
    alignItems: "center",
    gap: 10,
    padding: "10px 14px",

    background: "var(--accent)",
    color: "#fff",
    fontWeight: 900,
    textDecoration: "none",
    boxShadow: "0 10px 30px rgba(0,0,0,.14)",
};

const ghostCtaStyle: React.CSSProperties = {
    display: "inline-flex",
    alignItems: "center",
    gap: 10,
    padding: "10px 14px",

    border: "1px solid var(--line)",
    background: "rgba(255,255,255,.04)",
    color: "var(--text)",
    fontWeight: 900,
    textDecoration: "none",
};
