"use client";
import { useEffect, useState } from "react";


export default function DossierLikeButton({ slug }: { slug: string }) {
  const [likes, setLikes] = useState(0);
  const [liked, setLiked] = useState(false);

  useEffect(() => {
    fetch(`/api/dossiers/${slug}/reactions`)
      .then(r => r.json())
      .then(j => {
        setLikes(j.likes || 0);
        setLiked(j.liked || false);
      });
  }, [slug]);

  async function toggle() {
    const r = await fetch(`/api/dossiers/${slug}/like`, { method: "POST" });
    const j = await r.json();
    setLikes(j.likes);
    setLiked(j.liked);
  }

  return (
    <button onClick={toggle} className={`likePill ${liked ? "liked" : ""}`}>
      <i className="fa-solid fa-thumbs-up" />
      <span>{likes}</span>
    </button>
  );
}
