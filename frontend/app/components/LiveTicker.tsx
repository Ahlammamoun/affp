"use client";

import Link from "next/link";
import { useMemo, useState } from "react";

type LiveItem = { time: string; tag?: string; title: string; href?: string | null };

export default function LiveTicker({
  items,
  pageSize = 5,
}: {
  items: LiveItem[];
  pageSize?: number;
}) {
  const [start, setStart] = useState(0);

  const maxStart = Math.max(0, items.length - pageSize);

  const slice = useMemo(() => {
    return items.slice(start, start + pageSize);
  }, [items, start, pageSize]);

  const canUp = start > 0;
  const canDown = start < maxStart;

  return (
    <div className="liveBox">
      <div className="liveControls">
        <button
          className="arrowBtn"
          type="button"
          onClick={() => canUp && setStart((s) => Math.max(0, s - 1))}
          disabled={!canUp}
          aria-label="Défiler vers le haut"
        >
          ↑
        </button>

        <button
          className="arrowBtn"
          type="button"
          onClick={() => canDown && setStart((s) => Math.min(maxStart, s + 1))}
          disabled={!canDown}
          aria-label="Défiler vers le bas"
        >
          ↓
        </button>
      </div>

      <div className="liveList" role="list">
        {slice.map((it, idx) => (
          <div className="scrollRow" key={`${it.time}-${idx}`} role="listitem">
            <div className="timeTag">{it.time}</div>

            {/* ✅ badge au-dessus + texte aéré */}
            <div className="liveText">
              {it.tag ? (
                <span className={`badge ${it.tag === "Alerte" ? "badgeAlert" : ""}`}>
                  {it.tag}
                </span>
              ) : null}

              <div className="liveTitle">
                {it.href ? (
                  it.href.startsWith("/") ? (
                    <Link href={it.href}>{it.title}</Link>
                  ) : (
                    <a href={it.href} target="_blank" rel="noreferrer">
                      {it.title}
                    </a>
                  )
                ) : (
                  it.title
                )}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
