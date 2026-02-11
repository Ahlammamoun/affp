"use client";

import { useEffect, useMemo, useRef, useState } from "react";

type AdItem = {
  badge?: string; // "Publicité" | "Sponsorisé"
  title: string;
  text: string;
  href: string;
};

export default function AdCarousel({
  items,
  autoPlayMs = 4500,
}: {
  items: AdItem[];
  autoPlayMs?: number;
}) {
  const viewportRef = useRef<HTMLDivElement | null>(null);
  const trackRef = useRef<HTMLDivElement | null>(null);

  const [paused, setPaused] = useState(false);
  const [index, setIndex] = useState(0);
  const [stepPx, setStepPx] = useState(0);
  const [maxIndex, setMaxIndex] = useState(0);

  const canRun = useMemo(() => items.length > 0, [items.length]);

  // Mesure la largeur d'une slide + gap, puis calcule combien de slides sont visibles
  useEffect(() => {
    if (!canRun) return;

    const viewport = viewportRef.current;
    const track = trackRef.current;
    if (!viewport || !track) return;

    const measure = () => {
      const first = track.querySelector<HTMLElement>(".adSlide");
      if (!first) return;

      const cs = window.getComputedStyle(track);
      const gap = parseFloat(cs.gap || cs.columnGap || "0") || 0;

      const slideW = first.getBoundingClientRect().width;
      const vpW = viewport.getBoundingClientRect().width;

      const step = Math.max(1, Math.round(slideW + gap));
      setStepPx(step);

      // combien de cartes rentrent réellement dans la viewport
      const visible = Math.max(1, Math.floor((vpW + gap) / step));
      const max = Math.max(0, items.length - visible);
      setMaxIndex(max);

      // si on a réduit l'écran et que l'index devient trop grand
      setIndex((i) => Math.min(i, max));
    };

    measure();

    // ResizeObserver (si dispo)
    const ro = new ResizeObserver(() => measure());
    ro.observe(viewport as Element);
    ro.observe(track as Element);

    window.addEventListener("resize", measure);

    return () => {
      ro.disconnect();
      window.removeEventListener("resize", measure);
    };
  }, [canRun, items.length]);

  const next = () => setIndex((i) => (i >= maxIndex ? 0 : i + 1));
  const prev = () => setIndex((i) => (i <= 0 ? maxIndex : i - 1));

  // Auto-play
  useEffect(() => {
    if (!canRun || paused || autoPlayMs <= 0) return;
    const id = window.setInterval(() => next(), autoPlayMs);
    return () => window.clearInterval(id);
  }, [canRun, paused, autoPlayMs, maxIndex]);

  if (!canRun) return null;

  const offset = stepPx * index;

  return (
    <div
      className="adCarousel"
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
    >
      <button
        className="arrowBtn"
        type="button"
        aria-label="Précédent"
        onClick={prev}
      >
        ‹
      </button>

      {/* viewport qui masque tout débordement => zéro scroll horizontal */}
      <div className="adViewport" ref={viewportRef}>
        <div
          className="adTrack"
          ref={trackRef}
          style={{ transform: `translate3d(-${offset}px, 0, 0)` }}
        >
          {items.map((it, idx) => (
            <a key={idx} className="adSlide" href={it.href}>
              <div className="adBadge">{it.badge ?? "Publicité"}</div>
              <div className="adTitle">{it.title}</div>
              <div className="adText">{it.text}</div>
              <div className="adCta">Découvrir →</div>
            </a>
          ))}
        </div>
      </div>

      <button
        className="arrowBtn"
        type="button"
        aria-label="Suivant"
        onClick={next}
      >
        ›
      </button>

    </div>
  );
}
