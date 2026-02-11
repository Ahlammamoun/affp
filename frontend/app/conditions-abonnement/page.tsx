// app/conditions-abonnement/page.tsx
import Link from "next/link";

export default function ConditionsAbonnementPage() {
    return (
        <div className="container">
            <div className="pageGrid">
                <div className="stack" style={{ gridColumn: "1 / -1" }}>
                    <section className="frame" style={{ borderColor: "var(--accent)" }}>
                        <div className="frameHead">
                            <div className="frameTitle">Conditions d’abonnement</div>
                            <Link className="frameLink" href="/mentions-legales">
                                Mentions légales
                            </Link>
                        </div>

                        <div className="frameBody" style={{ padding: 20 }}>
                            <div
                                style={{
                                    display: "inline-flex",
                                    alignItems: "center",
                                    gap: 8,
                                    padding: "6px 10px",
                                    border: "1px solid var(--line)",
                                    background: "rgba(0,0,0,.04)",
                                    color: "var(--accent)",
                                    fontWeight: 900,
                                    letterSpacing: "0.08em",
                                    textTransform: "uppercase",
                                    fontSize: 12,
                                }}
                            >
                                ⭐ Abonnement & accès premium
                            </div>

                            <h1 style={{ marginTop: 12, fontSize: 28, lineHeight: 1.2, fontWeight: 950 }}>
                                Conditions d’abonnement
                            </h1>

                            <div className="meta" style={{ marginTop: 8 }}>
                                Dernière mise à jour :{" "}
                                <span suppressHydrationWarning>{new Date().toLocaleDateString("fr-FR")}</span>
                            </div>

                            <div style={{ marginTop: 16, display: "grid", gap: 14 }}>
                                <Clause n="1" title="Objet">
                                    Ces conditions définissent les modalités de souscription, de paiement, de renouvellement et
                                    d’accès aux contenus proposés sur <strong>[Nom du site]</strong>. En souscrivant, l’utilisateur
                                    accepte ces conditions.
                                </Clause>

                                <Clause n="2" title="Offres & accès">
                                    Les offres (mensuelle) sont décrites sur la page d’abonnement.
                                    L’accès aux contenus premium est réservé aux abonnés disposant d’un abonnement actif.
                                    <ul style={ulStyle}>
                                        <li>Accès activé après confirmation du paiement (délais bancaires possibles).</li>
                                        <li>Compte personnel : accès non transférable, sauf offre équipe/entreprise.</li>
                                        <li>Partage abusif : suspension possible selon les règles de sécurité.</li>
                                    </ul>
                                </Clause>

                                <Clause n="3" title="Prix">
                                    Les prix sont indiqués en <strong>2.9 EUR mensuel</strong> (TTC/HT selon configuration). Les tarifs peuvent
                                    évoluer. Toute modification s’applique au renouvellement suivant, après information dans un
                                    délai raisonnable.
                                </Clause>

                                <Clause n="4" title="Paiement">
                                    Le paiement s’effectue via <strong>[Stripe]</strong>. En cas d’échec de paiement au
                                    renouvellement, l’accès peut être suspendu jusqu’à régularisation.
                                </Clause>

                                <Clause n="5" title="Renouvellement automatique">
                                    Sauf mention contraire, l’abonnement est reconduit automatiquement à chaque échéance. Le
                                    renouvellement peut être désactivé depuis l’espace compte, au plus tard avant la date de
                                    renouvellement.
                                </Clause>

                                <Clause n="6" title="Résiliation">
                                    La résiliation arrête le renouvellement. L’accès reste disponible jusqu’à la fin de la période
                                    payée. Sauf disposition légale contraire, aucun remboursement n’est dû pour une période entamée.
                                </Clause>

                                <Clause n="7" title="Droit de rétractation">
                                    Selon votre pays, un droit de rétractation peut s’appliquer. Pour les contenus numériques
                                    fournis immédiatement, l’abonné peut être amené à renoncer à ce droit lors de la souscription
                                    (ex : accès instantané). Adaptez cette clause à votre parcours d’achat.
                                </Clause>

                                <Clause n="8" title="Suspension / maintenance">
                                    L’éditeur peut interrompre temporairement le service pour maintenance, sécurité, ou en cas de
                                    violation des règles (fraude, partage abusif, etc.).
                                </Clause>

                                <Clause n="9" title="Données personnelles">
                                    Les données sont traitées conformément à la{" "}
                                    <Link href="/politique-de-confidentialite" style={linkStyle}>
                                        Politique de confidentialité
                                    </Link>
                                    .
                                </Clause>

                                <Clause n="10" title="Support">
                                    Support abonnement : <strong>[support@domaine.com]</strong>
                                </Clause>

                                <Clause n="11" title="Droit applicable">
                                    Les présentes conditions sont régies par le droit de <strong>[Pays]</strong>. En cas de litige,
                                    les tribunaux compétents seront ceux de <strong>[Ville]</strong>, sauf dispositions impératives
                                    contraires.
                                </Clause>
                            </div>

                            <div
                                style={{
                                    marginTop: 18,
                                    padding: "12px 14px",
                                    borderLeft: "3px solid var(--accent)",

                                    background: "rgba(0,0,0,0.03)",
                                    fontStyle: "italic",
                                }}
                            >
                                “On explique les enjeux derrière les faits — accès réservé aux abonnés.”
                            </div>

                            <div style={{ marginTop: 18, display: "flex", gap: 10, flexWrap: "wrap" }}>
                                <Link href="/premium" style={ctaStyle}>
                                    Découvrir l’offre premium <span aria-hidden>→</span>
                                </Link>
                                <Link href="/mentions-legales" style={ghostCtaStyle}>
                                    Mentions légales <span aria-hidden>→</span>
                                </Link>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    );
}

function Clause({ n, title, children }: { n: string; title: string; children: React.ReactNode }) {
    return (
        <div
            style={{
                border: "1px solid var(--line)",
  
                padding: 14,
                background: "rgba(255,255,255,.02)",
            }}
        >
            <div style={{ display: "flex", gap: 10, alignItems: "baseline" }}>
                <div
                    style={{
                        minWidth: 28,
                        height: 28,

                        display: "inline-flex",
                        alignItems: "center",
                        justifyContent: "center",
                        fontWeight: 950,
                        border: "1px solid var(--line)",
                        background: "rgba(255,255,255,.04)",
                    }}
                >
                    {n}
                </div>
                <div style={{ fontWeight: 950 }}>{title}</div>
            </div>

            <div style={{ marginTop: 8, lineHeight: 1.75, color: "var(--muted)" }}>{children}</div>
        </div>
    );
}

const ulStyle: React.CSSProperties = {
    margin: "10px 0 0",
    paddingLeft: 18,
    display: "grid",
    gap: 6,
};

const linkStyle: React.CSSProperties = {
    textDecoration: "none",
    fontWeight: 900,
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
