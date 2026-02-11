"use client";

import { useEffect, useMemo, useState } from "react";

type Media = {
  fileName: string | null;
  url: string | null;
};

type Props = {
  title: string;
  thumb?: string | null;
  media?: Media[];
};

function resolveSrc(src?: string | null) {
  if (!src) return null;
  if (src.startsWith("http")) return src;
  if (src.startsWith("/")) return src;
  return `/uploads/media/${src}`;
}

export default function DestinationImage({ title, thumb, media }: Props) {
  const images = useMemo(() => {
    if (media?.length) {
      return media
        .map((m) => resolveSrc(m.fileName || m.url))
        .filter(Boolean) as string[];
    }
    return thumb ? [resolveSrc(thumb)!] : [];
  }, [media, thumb]);

  const [index, setIndex] = useState(0);
  const [flipping, setFlipping] = useState(false);

  const next = images.length ? (index + 1) % images.length : 0;

  useEffect(() => {
    if (images.length <= 1) return;

    const id = setInterval(() => {
      setFlipping(true);

      setTimeout(() => {
        setIndex((i) => (i + 1) % images.length);
        setFlipping(false);
      }, 450);
    }, 3500);

    return () => clearInterval(id);
  }, [images.length]);

  if (images.length === 0) {
    return (
      <div
        style={{
          flex: "0 0 50%",
          maxWidth: "53%",
          height: 250,
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
    );
  }

  return (
    <div
      style={{
        flex: "0 0 50%",
        maxWidth: "53%",
        height: 250,
        borderRadius: 10,
        overflow: "hidden",
        border: "1px solid var(--line)",
        background: "#00000008",
        perspective: 1200,
      }}
    >
      <div
        style={{
          position: "relative",
          width: "100%",
          height: "100%",
          transformStyle: "preserve-3d",
        transition: "transform 1.0s ease-in-out",

          transform: flipping ? "rotateY(-180deg)" : "rotateY(0deg)",
        }}
      >
        {/* page courante */}
        <div
          style={{
            position: "absolute",
            inset: 0,
            backfaceVisibility: "hidden",
          }}
        >
          <img
            src={images[index]}
            alt={title}
            style={{
              width: "100%",
              height: "100%",
              objectFit: "cover",
              display: "block",
            }}
          />
        </div>

        {/* page suivante */}
        <div
          style={{
            position: "absolute",
            inset: 0,
            transform: "rotateY(180deg)",
            backfaceVisibility: "hidden",
          }}
        >
          <img
            src={images[next]}
            alt={title}
            style={{
              width: "100%",
              height: "100%",
              objectFit: "cover",
              display: "block",
            }}
          />
        </div>
      </div>
    </div>
  );
}

