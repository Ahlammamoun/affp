import Link from "next/link";

export default function CancelPage() {
  return (
    <div className="container" style={{ paddingTop: 30, paddingBottom: 60 }}>
      <section className="frame">
        <div className="frameHead">
          <div className="frameTitle">Paiement annulé</div>
          <div className="frameLink">Abonnement</div>
        </div>
        <div className="frameBody" style={{ color: "var(--muted)" }}>
          Le paiement a été annulé. Vous pouvez réessayer quand vous voulez.
          <div style={{ marginTop: 12, display: "flex", gap: 10, flexWrap: "wrap" }}>
            <Link className="btn btnPrimary" href="/abonnement">Réessayer</Link>
            <Link className="btn" href="/">Retour accueil</Link>
          </div>
        </div>
      </section>
    </div>
  );
}
